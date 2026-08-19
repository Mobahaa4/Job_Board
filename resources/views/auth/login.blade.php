@extends('layouts.app')

@section('title', 'Login - AI Job Board')

@section('content')
<div class="row justify-content-center align-items-center min-vh-75">
    <div class="col-md-6 col-lg-5 col-xl-4">
        <div class="card auth-card">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <span class="brand-icon mb-3" style="width:56px;height:56px;font-size:1.75rem;"><i class="bi bi-box-arrow-in-right"></i></span>
                    <h2 class="fw-bold mb-1">Welcome back</h2>
                    <p class="text-muted mb-0">Login to your account.</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-box-arrow-in-right me-1"></i>Login</button>
                    <p class="text-center text-muted mt-3 mb-0 small">
                        Don't have an account? <a href="{{ route('register') }}">Register</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
