<?php

namespace App\Http\Controllers\User; 

use App\Http\Controllers\Controller; 
use App\Models\Order; 
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\PaymentTransaction;
use App\Services\GHNService; 
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class OrderController extends Controller 
{ 
    public function index() 
    { 
        $cart = session('cart', []); 
        if (empty($cart)) { 
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.'); 
        } 
        $totalPrice = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']); 

        return view('payment.index', compact('cart', 'totalPrice')); 
    } 

    public function processPayment(Request $request, GHNService $ghn, GHNOrderService $ghnOrders) 
    { 
        $request->validate([ 
            'name'           => 'required|string|max:100', 
            'phone'          => ['required', 'regex:/^0\d{9}$/'], 
            'address'        => 'required|string|max:255', 
            'province'       => 'required|string|max:255',
            'district'       => 'required|string|max:255',
            'ward'           => 'required|string|max:255',
            'to_district_id' => 'required|integer', 
            'to_ward_code'   => 'required|string', 
            'payment_method' => 'required|in:cod,momo', 
        ]);
        
        $cart = session('cart', []); 
        if (empty($cart)) { 
            return redirect()->route('cart.index')->with('error', 'Không thể thanh toán vì giỏ hàng trống.');
        } 

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']); 
        
        $weight = collect($cart)->sum(function ($item) {
            return (int)($item['weight'] ?? 200) * (int)$item['quantity'];
        });

        $feeResponse = $ghn->calculateFee(array_merge([ 
            'from_district_id' => (int) config('services.ghn.from_district_id'), 
            'to_district_id'   => (int) $request->to_district_id, 
            'to_ward_code'     => (string) $request->to_ward_code, 
        ], $ghn->packageParameters($weight)));
        
        $shippingFee = (isset($feeResponse['code']) && $feeResponse['code'] == 200) ? (int) $feeResponse['data']['total'] : 0;
        $finalTotal = $subtotal + $shippingFee;

        $order = DB::transaction(function () use ($request, $shippingFee, $finalTotal, $cart) { 
            $order = Order::create([ 
                'user_id'         => Auth::id(), 
                'fullname'        => $request->name, 
                'email'           => Auth::user()->email ?? $request->email ?? 'no-email@local.test', 
                'address'         => $request->address, 
                'phone'           => $request->phone, 
                'province'        => $request->province, 
                'district'        => $request->district, 
                'ward'            => $request->ward, 
                'payment_method'  => $request->payment_method,
                'total_price'     => $finalTotal, 
                'status'          => 'pending', 
                'to_district_id'  => (int) $request->to_district_id, 
                'to_ward_code'    => (string) $request->to_ward_code, 
                'ghn_total_fee'   => $shippingFee, 
                'shipping_status' => 'ready_to_pick', 
            ]);
            
            foreach ($cart as $id => $item) { 
                OrderItem::create([ 
                    'order_id'     => $order->id, 
                    'category_id'  => $item['category_id'] ?? ($id ?? 1), 
                    'product_name' => $item['name'] ?? ($item['product_name'] ?? 'Sản phẩm'), 
                    'quantity'     => $item['quantity'], 
                    'price'        => $item['price'], 
                ]);

                $product = Category::find($id);
                if ($product && method_exists($product, 'decrement')) {
                    $product->decrement('stock', $item['quantity']);
                }
            } 
            return $order; 
        });

        session()->forget('cart');

        $order->load('items'); 
        
        // Gọi API sang GHN để lấy mã vận đơn
        $ghnOrderResponse = $ghnOrders->create($order);
        
        if (is_array($ghnOrderResponse) && ($ghnOrderResponse['code'] ?? null) == 200 && !empty($ghnOrderResponse['data']['order_code'])) { 
            $order->update([ 
                'ghn_order_code'  => $ghnOrderResponse['data']['order_code'], 
                'shipping_status' => 'ready_to_pick', 
            ]);
        } else {
            Log::warning('GHN Order Creation Failed:', [
                'response' => $ghnOrderResponse,
                'order_id' => $order->id
            ]);
        }

        if ($request->payment_method === 'momo') { 
            PaymentTransaction::create([ 
                'order_id' => $order->id, 
                'gateway'  => 'momo', 
                'amount'   => $order->total_price, 
                'status'   => 'pending', 
            ]);

            return redirect()->route('user.orders.momo.start', $order);
        } 

        PaymentTransaction::create([ 
            'order_id' => $order->id, 
            'gateway'  => 'cod', 
            'amount'   => $order->total_price, 
            'status'   => 'pending', 
            'message'  => 'Thanh toán khi nhận hàng', 
        ]);

        return redirect()->route('user.orders.show', $order)->with('success', 'Đặt hàng thành công!');
    }

    public function orderHistory() 
    { 
        $orders = Order::where('user_id', Auth::id()) 
            ->with(['items']) 
            ->orderByDesc('created_at') 
            ->paginate(10); 

        return view('payment.order', compact('orders')); 
    } 

    public function show(Order $order) 
    { 
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) { 
            abort(403); 
        } 
        $order->load(['items']); 

        return view('payment.show', compact('order')); 
    } 

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Đơn hàng này không thể hủy vì đã được xử lý.');
        }

        DB::transaction(function () use ($order) {
            $order->update([
                'status' => 'cancelled',
                'shipping_status' => 'cancel',
            ]);

            foreach ($order->items as $item) {
                $product = Category::find($item->category_id);
                if ($product && method_exists($product, 'increment')) {
                    $product->increment('stock', $item->quantity);
                }
            }
        });

        return redirect()->back()->with('success', 'Đã hủy đơn hàng thành công.');
    }
}