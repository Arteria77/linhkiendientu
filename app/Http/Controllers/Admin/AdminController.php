<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalRevenue = (float) Order::sum('total_price');
        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $recentOrders = Order::with('user')->latest()->take(5)->get();

        $monthlyRevenue = Order::query()
            ->select('created_at', 'total_price')
            ->get()
            ->groupBy(function ($order) {
                return $order->created_at?->format('Y-m') ?? 'unknown';
            })
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'total' => (float) $items->sum('total_price'),
                    'orders' => $items->count(),
                ];
            })
            ->values();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalOrders',
            'totalRevenue',
            'pendingOrders',
            'completedOrders',
            'recentOrders',
            'monthlyRevenue'
        ));
    }

    public function users()
    {
        $users = User::latest()->paginate(15);

        return view('admin.users', compact('users'));
    }

    public function toggleUserLock(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Bạn không thể tự khoá tài khoản của chính mình.');
        }

        $user->update([
            'is_locked' => ! $user->is_locked,
        ]);

        return back()->with('success', $user->is_locked ? 'Tài khoản đã bị khoá.' : 'Tài khoản đã được mở khoá.');
    }

    public function orders()
    {
        $orders = Order::with(['user', 'orderItems'])->latest()->paginate(15);

        return view('admin.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ], [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function sales()
    {
        $orders = Order::all();
        $totalRevenue = (float) $orders->sum('total_price');
        $totalOrders = $orders->count();

        $monthlyRevenue = $orders
            ->groupBy(function ($order) {
                return $order->created_at?->format('Y-m') ?? 'unknown';
            })
            ->map(function ($items, $month) {
                return [
                    'month' => $month,
                    'total' => (float) $items->sum('total_price'),
                    'orders' => $items->count(),
                ];
            })
            ->values();

        $statusSummary = $orders
            ->groupBy('status')
            ->map(function ($items, $status) {
                return [
                    'status' => $status,
                    'orders' => $items->count(),
                    'revenue' => (float) $items->sum('total_price'),
                ];
            })
            ->values();

        $topProducts = OrderItem::all()
            ->groupBy('product_name')
            ->map(function ($items, $name) {
                return [
                    'name' => $name,
                    'quantity' => $items->sum('quantity'),
                    'revenue' => (float) $items->sum(function ($item) {
                        return $item->quantity * $item->price;
                    }),
                ];
            })
            ->sortByDesc('quantity')
            ->take(8)
            ->values();

        return view('admin.sales', compact(
            'totalRevenue',
            'totalOrders',
            'monthlyRevenue',
            'statusSummary',
            'topProducts'
        ));
    }
}
