<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý người dùng</title>
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
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Quản lý người dùng</h2>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Quay lại dashboard</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'primary' : 'secondary' }} text-white">
                                            {{ $user->role === 'admin' ? 'Admin' : 'User' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_locked ? 'danger' : 'success' }} text-white">
                                            {{ $user->is_locked ? 'Đã khoá' : 'Hoạt động' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '---' }}</td>
                                    <td>
                                        @if(auth()->id() !== $user->id)
                                            <form action="{{ route('admin.users.toggle-lock', $user->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-{{ $user->is_locked ? 'success' : 'warning' }} btn-sm">
                                                    {{ $user->is_locked ? 'Mở khoá' : 'Khoá' }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">Chưa có người dùng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</body>
</html>
