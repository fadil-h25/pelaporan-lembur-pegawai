@extends('layouts.app')
@section('content')
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-4">
            <div class="card p-4">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-primary">Silakan Login</h3>
                    <p class="text-muted">Gunakan Email & Password Anda</p>
                </div>
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="nama@email.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required placeholder="******">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Masuk Ke Dashboard</button>
                </form>
            </div>
        </div>
    </div>
@endsection
