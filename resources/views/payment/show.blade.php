@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="bg-white p-4 rounded shadow-sm">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    <h3 class="fw-bold">Chi tiết đơn hàng</h3>
                    <p class="text-muted">Thông tin chi tiết về đơn hàng của bạn.</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="border rounded p-3 mb-4 bg-light">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Mã đơn hàng:</span>
                        <span class="fw-bold text-primary">#{{ $order->id }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Họ và tên:</span>
                        <span class="fw-semibold">{{ $order->fullname }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Số điện thoại:</span>
                        <span class="fw-semibold">{{ $order->phone }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Email:</span>
                        <span class="fw-semibold">{{ $order->email }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Địa chỉ giao hàng:</span>
                        <span class="fw-semibold text-end">
                            {{ $order->address }}
                            @if($order->ward), {{ $order->ward }} @endif
                            @if($order->district), {{ $order->district }} @endif
                            @if($order->province), {{ $order->province }} @endif
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phương thức thanh toán:</span>
                        <span>
                            @if($order->payment_method == 'cod')
                                <span class="badge bg-primary">Thanh toán khi nhận hàng (COD)</span>
                            @else
                                <span class="badge bg-secondary" style="background-color: #a50064 !important;">Ví điện tử MoMo</span>
                            @endif
                        </span>
                    </div>

                    <!-- Bổ sung thông tin GHN và vận chuyển -->
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Phí vận chuyển (GHN):</span>
                        <span class="fw-semibold">{{ number_format($order->ghn_total_fee ?? 0, 0, ',', '.') }} đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Mã vận đơn GHN:</span>
                        <span class="fw-bold text-info">{{ $order->ghn_order_code ?? 'Đang cập nhật...' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Trạng thái vận chuyển:</span>
                        <span class="badge bg-warning text-dark">{{ $order->shipping_status ?? 'Đang chuẩn bị' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Trạng thái đơn hàng:</span>
                        <span class="badge {{ $order->status == 'cancelled' ? 'bg-danger' : 'bg-success' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold fs-6">Tổng tiền thanh toán:</span>
                        <span class="fw-bold fs-5 text-danger">{{ number_format($order->total_price, 0, ',', '.') }} đ</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h5 class="fw-bold mb-3">Sản phẩm đã đặt:</h5>
                    @if(isset($order->items) && count($order->items) > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($order->items as $item)
                                <div class="card border p-3 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $item->product_name }}</div>
                                            <small class="text-muted">SL: {{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }} đ</small>
                                        </div>
                                        <span class="fw-bold text-danger">
                                            {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">Không có sản phẩm nào trong đơn hàng này.</p>
                    @endif
                </div>

                <!-- Nút thao tác (Quay lại, Tiếp tục mua hàng và Hủy đơn hàng nếu ở trạng thái pending) -->
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('user.orders.index') }}" class="btn btn-secondary flex-fill py-2 fw-bold">Quay lại lịch sử</a>
                    
                    @if($order->status === 'pending')
                        <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" class="flex-fill" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">Hủy đơn hàng</button>
                        </form>
                    @endif

                    <a href="{{ route('welcome') }}" class="btn btn-outline-danger flex-fill py-2 fw-bold">Tiếp tục mua hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection