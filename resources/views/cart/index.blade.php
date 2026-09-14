@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold mb-4">Giỏ hàng của bạn</h3>

    @if(session('cart') && count(session('cart')) > 0)
        <div class="table-responsive bg-white rounded shadow-sm p-3">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 100px;">Hình ảnh</th>
                        <th>Tên linh kiện</th>
                        <th style="width: 140px;">Đơn giá</th>
                        <th style="width: 150px;">Số lượng</th>
                        <th style="width: 150px;">Thành tiền</th>
                        <th style="width: 100px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach(session('cart') as $id => $item)
                        @php 
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td>
                                @if(!empty($item['image']))
                                    <img src="{{ asset($item['image']) }}" class="img-thumbnail" style="width: 70px; height: 70px; object-fit: contain;" alt="{{ $item['name'] ?? '' }}">
                                @else
                                    <div class="bg-light border text-center py-3 text-muted rounded" style="width: 70px; height: 70px; font-size: 0.8rem;">No Img</div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark">{{ $item['name'] ?? 'Không xác định' }}</span>
                            </td>
                            <td>{{ number_format($item['price'], 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.update', $id) }}" method="POST" class="d-flex align-items-center gap-2">
                                    @csrf
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="form-control form-control-sm text-center" style="width: 65px;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Lưu</button>
                                </form>
                            </td>
                            <td class="fw-bold text-danger">{{ number_format($subtotal, 0, ',', '.') }} đ</td>
                            <td>
                                <form action="{{ route('cart.remove', $id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end align-items-center mt-3 gap-3">
            <h4 class="mb-0">Tổng tiền: <span class="text-danger fw-bold">{{ number_format($total, 0, ',', '.') }} đ</span></h4>
            <a href="{{ route('checkout.index') }}" class="btn btn-success fw-bold px-4 py-2">
                💳 Tiến hành thanh toán
            </a>
        </div>
    @else
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <p class="text-muted fs-5">Giỏ hàng của bạn đang trống!</p>
            <a href="{{ route('categories.index') }}" class="btn btn-danger">Tiếp tục mua hàng</a>
        </div>
    @endif
</div>
@endsection