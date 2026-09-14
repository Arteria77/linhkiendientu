@extends('layouts.app')

@section('content')
<div class="container-fluid py-3 px-md-4 bg-light">
    
    {{-- Header / Top Bar --}}
    <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded shadow-sm border-start border-4 border-danger">
        <div>
            <h4 class="fw-bold mb-0 text-uppercase text-dark">🔴 DANH MỤC LINH KIỆN MÁY TÍNH</h4>
            <small class="text-muted">Cung cấp linh kiện PC, Linh kiện điện tử chính hãng</small>
        </div>
        <div>
            {{-- Ẩn Giỏ hàng đối với Admin --}}
            @if(!auth()->check() || auth()->user()->role !== 'admin')
                <a href="{{ route('cart.index') }}" class="btn btn-danger fw-bold position-relative">
                    🛒 Giỏ hàng
                    @php $cartCount = count(session('cart', [])); @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>
            @endif
        </div>
    </div>

    <div class="row g-3">
        {{-- SIDEBAR: BỘ LỌC TÌM KIẾM CHUYÊN SÂU --}}
        <div class="col-lg-3 col-md-4">
            <div class="bg-white p-3 rounded shadow-sm sticky-top" style="top: 15px; z-index: 10;">
                
                {{-- Nút Xóa tất cả bộ lọc --}}
                @if(request('type') || request('brand') || request('socket'))
                    <div class="mb-3">
                        <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-danger w-100 fw-bold micro-text">
                            ✕ Xóa tất cả bộ lọc
                        </a>
                    </div>
                @endif

                {{-- Lọc theo Chủng loại --}}
                <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-2 text-danger">
                    📁 Chủng Loại Linh Kiện
                </h6>
                <div class="list-group list-group-flush mb-3">
                    <a href="{{ request()->fullUrlWithQuery(['type' => null]) }}" class="list-group-item list-group-item-action py-1.5 border-0 small fw-semibold {{ !request('type') ? 'text-danger bg-light' : 'text-dark' }}">
                        Tất cả sản phẩm
                    </a>
                    @foreach(['CPU', 'RAM', 'Mainboard', 'VGA', 'SSD', 'Linh kiện IC'] as $type)
                        <a href="{{ request()->fullUrlWithQuery(['type' => request('type') == $type ? null : $type]) }}" 
                           class="list-group-item list-group-item-action py-1.5 border-0 small d-flex justify-content-between align-items-center {{ request('type') == $type ? 'text-danger fw-bold bg-light' : 'text-secondary' }}">
                            <span>{{ $type }}</span>
                            <span>›</span>
                        </a>
                    @endforeach
                </div>

                {{-- Lọc theo Thương hiệu --}}
                <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-2 text-secondary">
                    🏷️ Thương Hiệu
                </h6>
                <div class="d-flex flex-wrap gap-1 mb-3">
                    @foreach(['Intel', 'AMD', 'Asus', 'MSI', 'Gigabyte', 'Kingston', 'Corsair', 'Samsung'] as $brand)
                        <a href="{{ request()->fullUrlWithQuery(['brand' => request('brand') == $brand ? null : $brand]) }}" 
                           class="btn btn-sm {{ request('brand') == $brand ? 'btn-danger' : 'btn-outline-secondary' }} py-0 px-2 micro-text">
                            {{ $brand }}
                        </a>
                    @endforeach
                </div>

                {{-- Lọc theo Chuẩn Socket / Kích thước --}}
                <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-2 text-secondary">
                    🔌 Socket / Chuẩn Kết Nối
                </h6>
                <div class="d-flex flex-wrap gap-1">
                    @foreach(['LGA 1700', 'AM5', 'DDR4', 'DDR5', 'PCIe 4.0', 'M.2 NVMe'] as $socket)
                        <a href="{{ request()->fullUrlWithQuery(['socket' => request('socket') == $socket ? null : $socket]) }}" 
                           class="btn btn-sm {{ request('socket') == $socket ? 'btn-dark' : 'btn-light border' }} py-0 px-2 micro-text">
                            {{ $socket }}
                        </a>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- MAIN CONTENT: DANH SÁCH SẢN PHẨM --}}
        <div class="col-lg-9 col-md-8">
            {{-- Toolbar điều hướng --}}
            <div class="bg-white p-2 px-3 rounded shadow-sm mb-3 d-flex justify-content-between align-items-center">
                <span class="small text-muted">Hiển thị <strong>{{ $categories->count() }}</strong> linh kiện</span>
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a class="btn btn-sm btn-success fw-semibold" href="{{ route('categories.create') }}">+ Thêm linh kiện mới</a>
                @endif
            </div>

            {{-- Lưới Sản Phẩm --}}
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-2">
                @forelse ($categories as $category)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm position-relative product-card bg-white">
                            
                            {{-- Badge Bảo hành & Tình trạng --}}
                            <div class="position-absolute top-0 start-0 m-2 d-flex flex-column gap-1" style="z-index: 2;">
                                <span class="badge bg-danger micro-badge">BH {{ $category->warranty_months ?? 36 }} Tháng</span>
                                @if($category->socket_type)
                                    <span class="badge bg-dark opacity-75 micro-badge">{{ $category->socket_type }}</span>
                                @endif
                            </div>

                            {{-- Image Container --}}
                            <div class="p-3 text-center bg-white rounded-top position-relative overflow-hidden" style="height: 180px;">
                                @if($category->image)
                                    <img src="{{ asset($category->image) }}" class="img-fluid mh-100 product-img" style="object-fit: contain;" alt="{{ $category->name }}">
                                @else
                                    <div class="bg-light h-100 w-100 d-flex align-items-center justify-content-center text-muted rounded">
                                        <small>[ {{ $category->category_type ?? 'Linh kiện' }} ]</small>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Detail --}}
                            <div class="card-body p-2.5 d-flex flex-column border-top">
                                <div class="d-flex justify-content-between align-items-center micro-text text-muted mb-1">
                                    <span>SKU: {{ $category->code ?? 'N/A' }}</span>
                                    <span class="fw-bold text-uppercase text-primary">{{ $category->brand }}</span>
                                </div>

                                <a href="{{ route('categories.show', $category->id) }}" class="text-decoration-none text-dark fw-bold small text-truncate-2 mb-1 product-title" title="{{ $category->name }}">
                                    {{ $category->name }}
                                </a>

                                <p class="card-text text-secondary micro-text mb-2 text-truncate-2" style="height: 2.1rem; line-height: 1.25;">
                                    {{ $category->specifications }}
                                </p>

                                <div class="mt-auto">
                                    {{-- Giá tiền --}}
                                    <div class="mb-2">
                                        <span class="text-danger fw-bold fs-6">
                                            {{ number_format($category->price, 0, ',', '.') }} đ
                                        </span>
                                    </div>

                                    {{-- Trạng thái & Thao tác (Admin hoặc Mua hàng) --}}
                                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                        <span class="micro-text fw-semibold {{ $category->stock > 0 ? 'text-success' : 'text-danger' }}">
                                            ● {{ $category->stock > 0 ? 'Còn hàng (' . $category->stock . ')' : 'Hết hàng' }}
                                        </span>

                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            {{-- Nút Sửa & Xóa dành cho Admin --}}
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-sm btn-outline-warning py-0 px-1.5 micro-text fw-bold">Sửa</a>
                                                
                                                <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa linh kiện này không?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1.5 micro-text fw-bold">Xóa</button>
                                                </form>
                                            </div>
                                        @elseif($category->stock > 0)
                                            {{-- Nút Mua ngay dành cho Khách hàng --}}
                                            <form action="{{ route('cart.add', $category->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2 fw-bold micro-text">
                                                    + MUA NGAY
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12 w-100 text-center py-5 bg-white rounded">
                        <p class="text-muted mb-0">Không tìm thấy linh kiện nào phù hợp với bộ lọc.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #eaeaea !important;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        border-color: #d70018 !important;
    }
    .product-title:hover {
        color: #d70018 !important;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .micro-text {
        font-size: 0.75rem;
    }
    .micro-badge {
        font-size: 0.65rem;
        padding: 0.25em 0.4em;
    }
</style>
@endsection