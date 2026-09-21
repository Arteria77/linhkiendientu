<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request, GHNService $ghnService)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // 1. Validate dữ liệu (Đã thêm 'momo' vào in:cod,banking,momo)
        $request->validate([
            'fullname'       => 'required|string|max:255',
            'phone'          => 'required|regex:/^[0-9]{10}$/',
            'email'          => 'required|email|max:255',
            'address'        => 'required|string|max:255',
            'province_id'    => 'required|integer',
            'to_district_id' => 'required|integer',
            'to_ward_code'   => 'required|string',
            'payment_method' => 'required|in:cod,banking,momo',
            'note'           => 'nullable|string|max:500',
        ], [
            'phone.regex'             => 'Số điện thoại phải bao gồm đúng 10 chữ số.',
            'phone.required'          => 'Vui lòng nhập số điện thoại.',
            'email.required'          => 'Vui lòng nhập email.',
            'email.email'             => 'Email không đúng định dạng.',
            'province_id.required'    => 'Vui lòng chọn Tỉnh/Thành phố.',
            'to_district_id.required' => 'Vui lòng chọn Quận/Huyện.',
            'to_ward_code.required'   => 'Vui lòng chọn Phường/Xã.',
        ]);

        $subTotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // Tính tổng khối lượng giỏ hàng
        $weight = collect($cart)->sum(function ($item) {
            return (int)($item['weight'] ?? config('services.ghn.default_weight', 200)) * (int)$item['quantity'];
        });

        // 2. Lấy phí vận chuyển từ GHN
        $feeData = $ghnService->calculateFee(array_merge([
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
        ], $ghnService->packageParameters($weight)));

        $shippingFee = $feeData['data']['total'] ?? 0;
        $totalPrice = $subTotal + $shippingFee;

        DB::beginTransaction();
        try {
            // 3. Tạo đơn hàng
            $order = Order::create([
                'user_id'         => auth()->id(),
                'fullname'        => $request->fullname,
                'phone'           => $request->phone,
                'email'           => $request->email,
                'address'         => $request->address,
                'country'         => 'VN',
                'province'        => $request->province_id,
                'to_district_id'  => $request->to_district_id,
                'to_ward_code'    => $request->to_ward_code,
                'note'            => $request->note,
                'payment_method'  => $request->payment_method,
                'ghn_total_fee'   => $shippingFee,
                'total_price'     => $totalPrice,
                'status'          => 'pending',
                'shipping_status' => 'ready_to_pick',
            ]);

            foreach ($cart as $id => $item) {
                // Tạo chi tiết đơn hàng
                OrderItem::create([
                    'order_id'     => $order->id,
                    'category_id'  => $id,
                    'product_name' => $item['name'] ?? 'Linh kiện',
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                ]);

                // Trừ số lượng tồn kho sử dụng đúng cột 'stock'
                $product = Category::find($id);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // 4. Đẩy thông tin đơn hàng sang API GHN để tạo mã vận đơn
            $ghnPayload = [
                'payment_type_id' => $request->payment_method === 'cod' ? 2 : 1,
                'note'            => $request->note ?? 'Hàng dễ vỡ',
                'required_note'   => 'KHONGCHOXEMHANG',
                'to_name'         => $order->fullname,
                'to_phone'        => $order->phone,
                'to_address'      => $order->address,
                'to_ward_code'    => (string) $order->to_ward_code,
                'to_district_id'  => (int) $order->to_district_id,
                'weight'          => $weight,
                'items'           => array_map(function ($item) {
                    return [
                        'name'     => $item['name'] ?? 'Sản phẩm',
                        'quantity' => (int) $item['quantity'],
                        'price'    => (int) $item['price'],
                    ];
                }, array_values($cart)),
            ];

            $ghnResponse = $ghnService->createOrder(array_merge($ghnPayload, $ghnService->packageParameters($weight)));

            if (isset($ghnResponse['data']['order_code'])) {
                $order->update([
                    'ghn_order_code' => $ghnResponse['data']['order_code'],
                ]);
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->route('checkout.success', $order->id)->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function success($id)
    {
        $order = Order::with('orderItems')->findOrFail($id);
        return view('checkout.success', compact('order'));
    }
}