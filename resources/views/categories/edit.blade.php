@extends('layouts.app')

@section('content')
<div class="container my-4" style="max-width: 600px;">
    <h2>Sửa thông tin linh kiện</h2>

    <form action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tên linh kiện:</label>
            <input type="text" name="name" value="{{ $category->name }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Thông số / Kỹ thuật:</label>
            <input type="text" name="specifications" value="{{ $category->specifications }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phân loại linh kiện:</label>
            <input type="text" name="brand" value="{{ $category->brand }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá bán (Đồng):</label>
            <input type="number" name="price" value="{{ $category->price }}" class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng tồn kho (Cái):</label>
            <input type="number" name="stock" value="{{ $category->stock }}" class="form-control" min="0" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh hiện tại:</label><br>
            @if($category->image)
                <img src="{{ asset('uploads/' . $category->image) }}" width="80" class="mb-2 rounded">
            @endif
            <input type="file" name="image" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
    </form>
</div>
@endsection