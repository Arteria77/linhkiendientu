<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê doanh số</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
            <div class="d-flex gap-2">
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Thống kê doanh số</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Quay lại dashboard</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted">Tổng doanh thu</div>
                        <h3 class="mt-2 mb-0">{{ number_format($totalRevenue, 0, ',', '.') }}đ</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body">
                        <div class="text-muted">Tổng số đơn hàng</div>
                        <h3 class="mt-2 mb-0">{{ $totalOrders }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <strong>Doanh thu theo tháng</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Tháng</th>
                                        <th>Số đơn</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($monthlyRevenue as $item)
                                        <tr>
                                            <td>{{ $item['month'] }}</td>
                                            <td>{{ $item['orders'] }}</td>
                                            <td>{{ number_format($item['total'], 0, ',', '.') }}đ</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">Chưa có dữ liệu doanh thu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <strong>Trạng thái đơn hàng</strong>
                    </div>
                    <div class="card-body">
                        @forelse ($statusSummary as $status)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-capitalize">{{ $status['status'] }}</span>
                                <strong>{{ number_format($status['revenue'], 0, ',', '.') }}đ</strong>
                            </div>
                        @empty
                            <p class="mb-0 text-muted">Chưa có dữ liệu.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white">
                <strong>Sản phẩm đã bán</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topProducts as $product)
                                <tr>
                                    <td>{{ $product['name'] }}</td>
                                    <td>{{ $product['quantity'] }}</td>
                                    <td>{{ number_format($product['revenue'], 0, ',', '.') }}đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Chưa có dữ liệu sản phẩm.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
