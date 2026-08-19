@extends('layouts.app')

@section('title', 'My Profile - AI Job Board')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                    <div>
                        <h3 class="fw-bold mb-0">My Profile</h3>
                        <p class="text-muted mb-0">Your public candidate profile.</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
                </div>

                <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-gradient-soft rounded-4" style="background: var(--gradient-soft);">
                    @if ($user->image)
                        <img src="{{ asset('storage/' . $user->image) }}" alt="Profile image" class="avatar-lg border bg-white">
                    @else
                        <span class="avatar-initial avatar-lg fs-3">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @endif
                    <div>
                        <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary-soft badge-soft"><i class="bi bi-person me-1"></i>{{ ucfirst($user->role) }}</span>
                            @if ($user->job_title)
                                <span class="badge bg-light text-dark border badge-soft"><i class="bi bi-briefcase me-1"></i>{{ $user->job_title }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-3 text-muted">Email</dt>
                    <dd class="col-sm-9">{{ $user->email }}</dd>

                    <dt class="col-sm-3 text-muted">Age</dt>
                    <dd class="col-sm-9">{{ $user->age ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted">Job Title</dt>
                    <dd class="col-sm-9">{{ $user->job_title ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted">Phone</dt>
                    <dd class="col-sm-9">{{ $user->phone ?? '—' }}</dd>

                    <dt class="col-sm-3 text-muted">Skills</dt>
                    <dd class="col-sm-9">
                        @if ($user->skillsList())
                            @foreach ($user->skillsList() as $skill)
                                <span class="badge bg-primary-soft badge-soft me-1 mb-1">{{ ucwords($skill) }}</span>
                            @endforeach
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-sm-3 text-muted">Description</dt>
                    <dd class="col-sm-9">{{ $user->description ?: '—' }}</dd>

                    <dt class="col-sm-3 text-muted">Resume (CV)</dt>
                    <dd class="col-sm-9">
                        @if ($user->resume)
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('resume.show', $user) }}" target="_blank" class="btn btn-outline-primary btn-sm" title="Opens in a new tab">
                                    <i class="bi bi-eye me-1"></i>View CV
                                </a>
                                <a href="{{ route('resume.download', $user) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-download me-1"></i>Download CV
                                </a>
                            </div>
                        @else
                            —
                        @endif
                    </dd>

                    <dt class="col-sm-3 text-muted">Member Since</dt>
                    <dd class="col-sm-9">{{ $user->created_at->format('d M Y') }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
