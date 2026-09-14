<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\User\GHNController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Trang chủ & Danh mục
Route::get('/', [CategoryController::class, 'index'])->name('welcome');
Route::resource('categories', CategoryController::class);

// Xác thực tài khoản (Đăng nhập / Đăng ký / Đăng xuất)
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// --- CÁC ROUTE XÁC THỰC EMAIL ---
// 1. Hiển thị thông báo yêu cầu xác thực email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. Xử lý khi người dùng nhấp vào link gửi về email
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome')->with('success', 'Xác thực email thành công!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. Gửi lại email xác thực
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Đã gửi lại liên kết xác thực vào email của bạn!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// Chức năng Giỏ hàng & Thanh toán (Bắt buộc phải đăng nhập và ĐÃ XÁC THỰC EMAIL)
Route::middleware(['auth', 'verified'])->group(function () {
    // Giỏ hàng
    Route::prefix('gio-hang')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/them/{id}', [CartController::class, 'add'])->name('add');
        Route::post('/cap-nhat/{id}', [CartController::class, 'update'])->name('update');
        Route::delete('/xoa/{id}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/xoa-tat-ca', [CartController::class, 'clear'])->name('clear');
    });

    // Thanh toán (Checkout)
    Route::prefix('thanh-toan')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'process'])->name('process');
        Route::get('/thanh-cong/{id}', [CheckoutController::class, 'success'])->name('success');
    });

    // GHN API Routes (Lấy địa chỉ & Tính phí vận chuyển)
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
        Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
        Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
        Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
    });
});

// Trang Quản trị (Admin)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        if (Auth::user()->role !== 'admin') {
            return redirect('/')->with('error', 'Bạn không có quyền truy cập!');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');
});