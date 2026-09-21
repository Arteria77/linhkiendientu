<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\MomoController;
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
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome')->with('success', 'Xác thực email thành công!');
})->middleware(['auth', 'signed'])->name('verification.verify');

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

    // Xử lý đơn hàng và thanh toán MoMo (User)
    Route::prefix('user')->name('user.')->group(function () {
        Route::post('/orders/process', [OrderController::class, 'processPayment'])->name('orders.process');
        Route::get('/orders', [OrderController::class, 'orderHistory'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/orders/momo/{order}/start', [MomoController::class, 'start'])->name('orders.momo.start');
        Route::get('/orders/momo/callback', [MomoController::class, 'callback'])->name('payment.momo.callback');
    });

    // GHN API Routes (Lấy địa chỉ & Tính phí vận chuyển)
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
        Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
        Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
        Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
    });
});

// Route nhận IPN từ MoMo (Nằm ngoài nhóm auth/verified vì MoMo server gọi trực tiếp vào)
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');

// Trang Quản trị (Admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/{user}/toggle-lock', [AdminController::class, 'toggleUserLock'])->name('users.toggle-lock');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders.index');
    Route::post('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::get('/sales', [AdminController::class, 'sales'])->name('sales.index');
});