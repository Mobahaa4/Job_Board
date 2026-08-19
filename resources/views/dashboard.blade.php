@extends('layouts.app')

@section('title', 'Dashboard - AI Job Board')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        @if (auth()->user()->image)
            <img src="{{ asset('storage/' . auth()->user()->image) }}" alt="" class="avatar-lg border">
        @else
            <span class="avatar-initial avatar-lg fs-3">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
        @endif
        <div>
            <h3 class="fw-bold mb-0">Welcome back, {{ auth()->user()->name }}!</h3>
            <p class="text-muted mb-0">{{ auth()->user()->job_title ?? 'Candidate' }} · <i class="bi bi-envelope me-1"></i>{{ auth()->user()->email }}</p>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="bi bi-search me-1"></i>Browse Jobs</a>
        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-primary-soft text-primary"><i class="bi bi-send"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['applications'] }}</div>
                <div class="text-muted small">Total Applications</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-success-soft text-success"><i class="bi bi-briefcase"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['openJobs'] }}</div>
                <div class="text-muted small">Open Jobs</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-warning-soft text-warning"><i class="bi bi-hourglass-split"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['pending'] }}</div>
                <div class="text-muted small">Pending Applications</div>
            </div>
        </div>
    </div>
</div>

@if ($recommended && $recommended->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><i class="bi bi-stars text-gradient me-1"></i>Recommended for you</span>
            <a href="{{ route('chatbot.index') }}" class="small text-decoration-none"><i class="bi bi-robot me-1"></i>Ask Jobot</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach ($recommended as $job)
                    <div class="col-md-4">
                        <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                            <div class="card job-card h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="job-icon" style="width:38px;height:38px;font-size:1rem;"><i class="bi bi-briefcase"></i></span>
                                        <h6 class="fw-bold text-dark mb-0">{{ $job->title }}</h6>
                                    </div>
                                    <p class="text-muted small mb-2"><i class="bi bi-geo-alt me-1"></i>{{ $job->location }} · {{ $job->workTypeLabel() }}</p>
                                    <span class="badge bg-primary-soft badge-soft">{{ $job->category }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Recent Applications</span>
        <a href="{{ route('applications.index') }}" class="small text-decoration-none">View all</a>
    </div>
    <div class="card-body p-0">
        @if ($recentApplications->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-briefcase display-4 d-block mb-2"></i>
                You haven't applied to any jobs yet.
                <a href="{{ route('jobs.index') }}" class="d-block mt-2 text-decoration-none">Browse available jobs</a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Job</th>
                            <th>Applied On</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentApplications as $application)
                            <tr>
                                <td>
                                    <a href="{{ route('jobs.show', $application->job) }}" class="text-decoration-none fw-medium">{{ $application->job->title }}</a>
                                    <div class="text-muted small">{{ $application->job->location }}</div>
                                </td>
                                <td>{{ $application->created_at->format('d M Y') }}</td>
                                <td>
                                    @php $classes = ['pending' => 'bg-warning-soft text-warning', 'accepted' => 'bg-success-soft text-success', 'rejected' => 'bg-danger-soft text-danger']; @endphp
                                    <span class="badge {{ $classes[$application->status] }} badge-soft">{{ $application->statusLabel() }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
