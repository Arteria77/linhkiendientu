@extends('layouts.app')

@section('content')
<div class="container py-4">
    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Cột trái: Thông tin người mua -->
            <div class="col-lg-7">
                <div class="bg-white p-4 rounded shadow-sm">
                    <h5 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person text-primary" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        Thông tin người mua
                    </h5>

                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="fullname" class="form-control" placeholder="Họ và tên" value="{{ old('fullname', auth()->user()->name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                            <input type="text" name="phone" maxlength="10" class="form-control" placeholder="Số điện thoại" value="{{ old('phone') }}" pattern="[0-9]{10}" title="Số điện thoại gồm 10 chữ số" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Địa chỉ <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" placeholder="Số nhà, tên đường..." value="{{ old('address') }}" required>
                        </div>
                        
                        <!-- Địa giới hành chính GHN -->
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                            <select name="province_id" id="province" class="form-select" required>
                                <option value="">Chọn Tỉnh / Thành phố</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Quận / Huyện <span class="text-danger">*</span></label>
                            <select name="to_district_id" id="district" class="form-select" required disabled>
                                <option value="">Chọn Quận / Huyện</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Phường / Xã <span class="text-danger">*</span></label>
                            <select name="to_ward_code" id="ward" class="form-select" required disabled>
                                <option value="">Chọn Phường / Xã</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Ghi chú (Tuỳ chọn)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Yêu cầu giao hàng / ghi chú thêm...">{{ old('note') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Phương thức thanh toán & Đơn hàng -->
            <div class="col-lg-5">
                <div class="bg-white p-4 rounded shadow-sm mb-3">
                    <h5 class="fw-bold mb-3 text-dark">Phương thức thanh toán</h5>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="cod">
                            🚚 Thanh toán khi nhận hàng (COD)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="banking" value="banking" {{ old('payment_method') == 'banking' ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="banking">
                            🏦 Chuyển khoản ngân hàng (Banking)
                        </label>
                    </div>
                </div>

                <div class="bg-white p-4 rounded shadow-sm">
                    <h5 class="fw-bold mb-3 text-dark">Tóm tắt đơn hàng</h5>
                    @php $subTotal = 0; @endphp
                    <div class="border-bottom pb-2 mb-2">
                        @foreach($cart as $item)
                            @php 
                                $sub = $item['price'] * $item['quantity'];
                                $subTotal += $sub;
                            @endphp
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <div class="fw-semibold small">{{ $item['name'] ?? 'Sản phẩm' }}</div>
                                    <small class="text-muted">SL: {{ $item['quantity'] }} x {{ number_format($item['price'], 0, ',', '.') }} đ</small>
                                </div>
                                <span class="fw-bold small">{{ number_format($sub, 0, ',', '.') }} đ</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Tạm tính:</span>
                        <span class="fw-semibold small">{{ number_format($subTotal, 0, ',', '.') }} đ</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="text-muted small">Phí vận chuyển (GHN):</span>
                        <span id="shipping-fee" class="fw-semibold small text-primary">0 đ</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-5">Tổng tiền:</span>
                        <span id="total-price" class="fw-bold fs-5 text-danger" data-subtotal="{{ $subTotal }}">{{ number_format($subTotal, 0, ',', '.') }} đ</span>
                    </div>

                    <button type="submit" class="btn btn-danger w-100 py-2.5 fw-bold text-uppercase fs-6">
                        Xác nhận đặt hàng
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');
    const wardSelect = document.getElementById('ward');
    const feeElement = document.getElementById('shipping-fee');
    const totalElement = document.getElementById('total-price');
    const subTotal = parseFloat(totalElement.getAttribute('data-subtotal')) || 0;

    // 1. Tải danh sách Tỉnh/Thành phố
    fetch("{{ route('locations.provinces') }}")
        .then(res => res.json())
        .then(res => {
            const data = res.data || res;
            if (Array.isArray(data)) {
                data.forEach(item => {
                    provinceSelect.innerHTML += `<option value="${item.ProvinceID}">${item.ProvinceName}</option>`;
                });
            }
        });

    // 2. Load Quận/Huyện khi chọn Tỉnh
    provinceSelect.addEventListener('change', function () {
        districtSelect.innerHTML = '<option value="">Chọn Quận / Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường / Xã</option>';
        wardSelect.disabled = true;
        resetFee();

        if (!this.value) { 
            districtSelect.disabled = true; 
            return; 
        }

        fetch(`/locations/districts/${this.value}`)
            .then(res => res.json())
            .then(res => {
                const data = res.data || res;
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        districtSelect.innerHTML += `<option value="${item.DistrictID}">${item.DistrictName}</option>`;
                    });
                    districtSelect.disabled = false;
                }
            });
    });

    // 3. Load Phường/Xã khi chọn Quận/Huyện
    districtSelect.addEventListener('change', function () {
        wardSelect.innerHTML = '<option value="">Chọn Phường / Xã</option>';
        resetFee();

        if (!this.value) { 
            wardSelect.disabled = true; 
            return; 
        }

        fetch(`/locations/wards/${this.value}`)
            .then(res => res.json())
            .then(res => {
                const data = res.data || res;
                if (Array.isArray(data)) {
                    data.forEach(item => {
                        wardSelect.innerHTML += `<option value="${item.WardCode}">${item.WardName}</option>`;
                    });
                    wardSelect.disabled = false;
                }
            });
    });

    // 4. Tính phí ship GHN khi chọn Phường/Xã
    wardSelect.addEventListener('change', function () {
        if (!this.value || !districtSelect.value) {
            resetFee();
            return;
        }

        feeElement.innerText = "Đang tính...";

        fetch("{{ route('locations.fee') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                to_district_id: districtSelect.value,
                to_ward_code: this.value
            })
        })
        .then(res => res.json())
        .then(res => {
            const fee = res.data?.total || res.total || 0;
            feeElement.innerText = formatMoney(fee);
            totalElement.innerText = formatMoney(subTotal + fee);
        })
        .catch(() => {
            feeElement.innerText = "Lỗi tính phí";
        });
    });

    function resetFee() {
        feeElement.innerText = "0 đ";
        totalElement.innerText = formatMoney(subTotal);
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }
});
</script>
@endsection