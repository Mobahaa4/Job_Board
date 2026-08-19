@extends('layouts.app')

@section('title', 'Edit Profile - AI Job Board')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4 p-lg-5">
                <h3 class="fw-bold mb-1">Edit Profile</h3>
                <p class="text-muted mb-4">Keep your information up to date for better job matches.</p>

                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age" value="{{ old('age', $user->age) }}" min="16" max="99">
                            @error('age') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="job_title" class="form-label">Job Title</label>
                            <input type="text" class="form-control @error('job_title') is-invalid @enderror" id="job_title" name="job_title" value="{{ old('job_title', $user->job_title) }}">
                            @error('job_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="skills" class="form-label">Skills</label>
                        <textarea class="form-control @error('skills') is-invalid @enderror" id="skills" name="skills" rows="3" placeholder="Separate skills with commas, e.g. PHP, Laravel, MySQL, HTML, CSS">{{ old('skills', $user->skills) }}</textarea>
                        <div class="form-text">Used by the AI chatbot to recommend suitable jobs.</div>
                        @error('skills') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Profile Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Tell employers about yourself and your experience...">{{ old('description', $user->description) }}</textarea>
                        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">Profile Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <div class="form-text">JPG, PNG or WebP. Max 2 MB.</div>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if ($user->image)
                                <div class="d-flex align-items-center gap-3 mt-2">
                                    <img src="{{ asset('storage/' . $user->image) }}" alt="Current image" class="avatar">
                                    <button type="submit" form="delete-image-form" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Remove</button>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="resume" class="form-label">Resume (CV)</label>
                            <input type="file" class="form-control @error('resume') is-invalid @enderror" id="resume" name="resume" accept=".pdf,.doc,.docx">
                            <div class="form-text">PDF, DOC or DOCX. Max 5 MB.</div>
                            @error('resume') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if ($user->resume)
                                <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                                    <a href="{{ route('resume.show', $user) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Opens in a new tab">
                                        <i class="bi bi-eye me-1"></i>View CV
                                    </a>
                                    <a href="{{ route('resume.download', $user) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-download me-1"></i>Download
                                    </a>
                                    <button type="submit" form="delete-resume-form" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Remove</button>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
                        <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>

                <form id="delete-image-form" method="POST" action="{{ route('profile.image.destroy') }}" class="d-none" data-confirm="Your profile picture will be permanently removed." data-confirm-title="Remove profile picture?">
                    @csrf
                    @method('DELETE')
                </form>
                <form id="delete-resume-form" method="POST" action="{{ route('profile.resume.destroy') }}" class="d-none" data-confirm="Your CV file will be permanently removed from your profile." data-confirm-title="Remove CV?">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
