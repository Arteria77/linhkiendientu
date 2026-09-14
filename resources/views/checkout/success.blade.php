@extends('layouts.app')

@section('content')
<div class="container py-5 text-center">
    <div class="bg-white p-5 rounded shadow-sm d-inline-block text-start" style="max-width: 650px; width: 100%;">
        <div class="text-center mb-4">
            <div class="text-success mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l5-5.25a.75.75 0 0 0-.022-1.06z"/>
                </svg>
            </div>
            <h3 class="fw-bold text-dark">Đặt hàng thành công!</h3>
            <p class="text-muted">Cảm ơn bạn đã tin tưởng và mua sắm tại cửa hàng.</p>
        </div>

        <div class="border rounded p-3 bg-light mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="fw-bold text-secondary">Mã đơn hàng:</span>
                <span class="fw-bold text-primary">#{{ $order->id }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Họ và tên:</span>
                <span class="fw-semibold">{{ $order->fullname }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Số điện thoại:</span>
                <span class="fw-semibold">{{ $order->phone }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Email:</span>
                <span class="fw-semibold">{{ $order->email }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Địa chỉ giao hàng:</span>
                <span class="fw-semibold text-end">{{ $order->address }}, {{ $order->province }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Phương thức thanh toán:</span>
                <span class="badge bg-info text-dark">
                    {{ $order->payment_method === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản ngân hàng (Banking)' }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                <span class="fw-bold fs-6">Tổng tiền thanh toán:</span>
                <span class="fw-bold fs-5 text-danger">{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold mb-3 text-dark">Sản phẩm đã đặt:</h6>
            <ul class="list-group list-group-flush border rounded">
                @foreach($order->orderItems as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            <small class="text-muted">SL: {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }} đ</small>
                        </div>
                        <span class="fw-bold text-dark">{{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="text-center">
            <a href="{{ route('welcome') }}" class="btn btn-danger px-4 py-2 fw-bold">
                Tiếp tục mua hàng
            </a>
        </div>
    </div>
</div>
@endsection