<?php
namespace App\Http\Controllers\User; 

use App\Http\Controllers\Controller; 
use App\Models\Order; 
use App\Services\GHNService; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\DB; 

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

    public function orderHistory() 
    { 
        $orders = Order::where('user_id', Auth::id()) 
            ->with(['items.product']) 
            ->orderByDesc('created_at') 
            ->paginate(10); 

        return view('payment.order', compact('orders')); 
    } 

    public function show(Order $order) 
    { 
        if ($order->user_id !== Auth::id() && !Auth::user()->is_admin) { 
            abort(403); 
        } 
        $order->load(['items.product']); 

        return view('payment.show', compact('order')); 
    } 

    public function cancel(Order $order, GHNService $ghn) 
    { 
        abort_unless($order->user_id === Auth::id(), 403); 
        $allowedStatuses = ['pending', 'ready_to_pick']; 

        if (!in_array($order->shipping_status, $allowedStatuses, true)) { 
            return back()->with('error', 'Đơn hàng không còn ở trạng thái có thể hủy.'); 
        } 

        if ($order->ghn_order_code) { 
            $response = $ghn->cancelOrder([$order->ghn_order_code]); 
            if (($response['code'] ?? null) !== 200) { 
                return back()->with('error', 'GHN không cho phép hủy vận đơn này.'); 
            } 
        } 

        DB::transaction(function () use ($order) { 
            $order->update([ 
                'status' => 'cancelled', 
                'shipping_status' => 'cancelled', 
            ]); 
        }); 

        return back()->with('success', 'Đơn hàng đã được hủy.'); 
    } 
}