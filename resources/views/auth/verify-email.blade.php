@extends('layouts.app')

@section('content')
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm p-4 text-center">
        <h2>Xác thực địa chỉ Email</h2>
        <p class="mt-3 text-muted">
            Vui lòng kiểm tra hòm thư Email của bạn và nhấp vào liên kết xác thực để hoàn tất đăng ký tài khoản.
        </p>

        {{-- Thông báo khi bấm nút gửi lại email --}}
        @if (session('message'))
            <div class="alert alert-success mt-2">
                {{ session('message') }}
            </div>
        @endif

        {{-- Form gửi lại email xác thực --}}
        <form method="POST" action="{{ route('verification.send') }}" class="mt-3">
            @csrf
            <button type="submit" class="btn btn-primary">Gửi lại email xác thực</button>
        </form>
    </div>
</div>
@endsection