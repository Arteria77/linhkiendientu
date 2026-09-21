<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <div class="d-flex gap-2">
                <a href="{{ route('categories.index') }}" class="btn btn-outline-light btn-sm">Quản lý danh mục</a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light btn-sm">Người dùng</a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-light btn-sm">Đơn hàng</a>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-outline-light btn-sm">Doanh số</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-4">
            <h2 class="mb-1">Xin chào, {{ auth()->user()->name }}!</h2>
            <p class="text-muted mb-0">Bảng điều khiển quản trị hệ thống.</p>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted">Người dùng</div>
                        <h3 class="mt-2 mb-0">{{ $totalUsers ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted">Đơn hàng</div>
                        <h3 class="mt-2 mb-0">{{ $totalOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted">Doanh thu</div>
                        <h3 class="mt-2 mb-0">{{ number_format($totalRevenue ?? 0, 0, ',', '.') }}đ</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted">Chờ xác nhận</div>
                        <h3 class="mt-2 mb-0">{{ $pendingOrders ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <strong>Đơn hàng gần đây</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Mã</th>
                                        <th>Khách hàng</th>
                                        <th>Giá trị</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentOrders ?? [] as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->fullname }}</td>
                                            <td>{{ number_format($order->total_price, 0, ',', '.') }}đ</td>
                                            <td>
                                                <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'cancelled' ? 'danger' : ($order->status === 'processing' ? 'warning' : 'secondary')) }} text-white">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">Chưa có đơn hàng nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="list-group shadow-sm">
                    <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">Quản lý người dùng</a>
                    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">Quản lý đơn hàng</a>
                    <a href="{{ route('admin.sales.index') }}" class="list-group-item list-group-item-action">Thống kê doanh số</a>
                    <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action">Quản lý danh mục</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>