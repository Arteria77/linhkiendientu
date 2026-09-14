@extends('layouts.app')

@section('content')
<div class="row justify-content-center my-4">
    <div class="col-md-5">
        <div class="card shadow-sm p-4">
            <h3 class="mb-3 text-center">Đăng ký tài khoản mới</h3>

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <p class="mb-0">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Đăng ký</button>
            </form>
            
            <div class="mt-3 text-center">
                <a href="{{ route('login') }}" class="text-decoration-none">Đã có tài khoản? Đăng nhập</a>
            </div>
        </div>
    </div>
</div>
@endsection