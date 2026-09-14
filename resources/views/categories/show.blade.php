@extends('layouts.app')

@section('content')
<div class="container my-4" style="max-width: 700px;">
    <h2>Chi tiết linh kiện</h2>

    <div class="card p-3">
        <div class="row">
            <div class="col-md-5 text-center">
                @if($category->image)
                    <img src="{{ asset('uploads/' . $category->image) }}" class="img-fluid rounded" alt="{{ $category->name }}">
                @else
                    <span class="text-muted">Chưa có ảnh</span>
                @endif
            </div>
            <div class="col-md-7">
                <h4>{{ $category->name }}</h4>
                <p><strong>Thông số / Kỹ thuật:</strong> {{ $category->specifications }}</p>
                <p><strong>Phân loại:</strong> {{ $category->brand }}</p>
                <p><strong>Giá bán:</strong> <span class="text-primary fw-bold">{{ number_format($category->price) }} đ</span></p>
                <p><strong>Tồn kho:</strong> {{ $category->stock }} cái</p>

                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Quay lại</a>
            </div>
        </div>
    </div>
</div>
@endsection