@extends('layouts.app')

@section('content')
<div class="container my-4" style="max-width: 600px;">
    <h2>Thêm linh kiện điện tử mới</h2>

    <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Tên linh kiện:</label>
            <input type="text" name="name" class="form-control" placeholder="VD: IC STM32F103C8T6, Tụ điện 10uF..." required>
        </div>

        <div class="mb-3">
            <label class="form-label">Thông số / Kỹ thuật:</label>
            <input type="text" name="specifications" class="form-control" placeholder="VD: 3.3V, DIP-8, SMD..." required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phân loại linh kiện:</label>
            <input type="text" name="brand" class="form-control" placeholder="VD: Vi điều khiển, Điện trở, Cảm biến..." required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá bán (Đồng):</label>
            <input type="number" name="price" class="form-control" min="0" placeholder="VD: 45000" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng tồn kho (Cái):</label>
            <input type="number" name="stock" class="form-control" min="0" placeholder="VD: 100" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh linh kiện:</label>
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-success">Lưu linh kiện</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection