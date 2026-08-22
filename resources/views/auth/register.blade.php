@extends('layouts.app')

@section('title', 'Register - AI Job Board')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card auth-card" style="max-width: 620px;">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <span class="brand-icon mb-3" style="width:56px;height:56px;font-size:1.75rem;"><i class="bi bi-person-plus"></i></span>
                    <h2 class="fw-bold mb-1">Create your account</h2>
                    <p class="text-muted mb-0">Join AI Job Board and start your journey.</p>
                </div>

                <form method="POST" action="{{ route('register.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">I want to register as</label>
                        <div class="d-flex gap-3">
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="role" id="roleCandidate" value="candidate" {{ old('role', 'candidate') === 'candidate' ? 'checked' : '' }} required onchange="toggleRoleFields()">
                                <label class="btn btn-outline-primary w-100 text-start p-3" for="roleCandidate">
                                    <div class="fw-semibold"><i class="bi bi-person me-1"></i>Candidate</div>
                                    <div class="small text-muted">Browse and apply for jobs</div>
                                </label>
                            </div>
                            <div class="flex-fill">
                                <input type="radio" class="btn-check" name="role" id="roleAdmin" value="admin" {{ old('role') === 'admin' ? 'checked' : '' }} onchange="toggleRoleFields()">
                                <label class="btn btn-outline-warning w-100 text-start p-3" for="roleAdmin">
                                    <div class="fw-semibold"><i class="bi bi-shield-lock me-1"></i>Admin</div>
                                    <div class="small text-muted">Manage jobs, candidates & applications</div>
                                </label>
                            </div>
                        </div>
                        @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>

                    <div id="candidate-fields">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age') }}" min="16" max="99">
                            @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="job_title" class="form-label">Job Title</label>
                            <input type="text" class="form-control @error('job_title') is-invalid @enderror" id="job_title" name="job_title" value="{{ old('job_title') }}" placeholder="e.g. Laravel Developer" required>
                            @error('job_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="skills" class="form-label">Skills</label>
                        <textarea class="form-control @error('skills') is-invalid @enderror" id="skills" name="skills" rows="2" placeholder="Separate skills with commas, e.g. PHP, Laravel, MySQL, HTML, CSS" required>{{ old('skills') }}</textarea>
                        <div class="form-text">Used by the AI chatbot to recommend suitable jobs.</div>
                        @error('skills') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-person-plus me-1"></i>Create Account</button>
                    <p class="text-center text-muted mt-3 mb-0 small">
                        Already have an account? <a href="{{ route('login') }}">Login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleRoleFields() {
        var isAdmin = document.getElementById('roleAdmin').checked;
        var fields = document.getElementById('candidate-fields');
        if (fields) {
            fields.style.display = isAdmin ? 'none' : '';
            fields.querySelectorAll('input, textarea').forEach(function (el) {
                el.disabled = isAdmin;
            });
        }
    }
    document.addEventListener('DOMContentLoaded', toggleRoleFields);
</script>
@endsection
