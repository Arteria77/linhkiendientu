<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Đăng xuất</button>
            </form>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Xin chào, {{ auth()->user()->name }}!</h2>
        <p>Đây là trang quản trị của bạn.</p>
        <div class="list-group">
            <a href="/categories" class="list-group-item list-group-item-action">Quản lý Danh mục</a>
        </div>
    </div>
</body>
</html>