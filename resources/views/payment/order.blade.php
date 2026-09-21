@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="fw-bold mb-4">Lịch sử đơn hàng của bạn</h2>

    @if($orders->isEmpty())
        <div class="alert alert-info text-center py-4">
            <p class="mb-3">Bạn chưa có đơn hàng nào.</p>
            <a href="{{ route('welcome') }}" class="btn btn-primary">Mua sắm ngay</a>
        </div>
    @else
        <div class="table-responsive bg-white rounded shadow-sm p-3">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Vận chuyển</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="fw-bold text-primary">#{{ $order->id }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="fw-bold text-danger">{{ number_format($order->total_price, 0, ',', '.') }} đ</td>
                            <td>
                                <span class="badge bg-secondary text-uppercase">{{ $order->payment_method }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark">{{ $order->shipping_status ?? 'pending' }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('user.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    Chi tiết
                                </a>
                                @if(in_array($order->shipping_status, ['pending', 'ready_to_pick']))
                                    <form action="{{ route('user.orders.cancel', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Hủy</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- CSS fix lỗi hiển thị mũi tên phân trang bị to/vỡ giao diện --}}
        <style>
            .pagination svg {
                width: 20px;
                height: 20px;
            }
            .pagination {
                display: flex;
                justify-content: center;
                gap: 5px;
            }
        </style>

        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection