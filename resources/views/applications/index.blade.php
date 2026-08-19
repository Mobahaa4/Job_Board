@extends('layouts.app')

@section('title', 'My Applications - AI Job Board')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0">My Applications</h3>
        <p class="text-muted mb-0">All the jobs you have applied for.</p>
    </div>
    <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="bi bi-search me-1"></i>Browse More Jobs</a>
</div>

@if ($applications->isEmpty())
    <div class="card">
        <div class="card-body text-center text-muted py-5">
            <i class="bi bi-send display-4 d-block mb-2"></i>
            You haven't applied to any jobs yet.
            <a href="{{ route('jobs.index') }}" class="d-block mt-2 text-decoration-none">Browse available jobs</a>
        </div>
    </div>
@else
    <div class="row g-3">
        @foreach ($applications as $application)
            <div class="col-md-6 col-lg-4">
                <div class="card job-card h-100">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <h5 class="fw-bold mb-0">
                                <a href="{{ route('jobs.show', $application->job) }}" class="text-decoration-none text-dark">{{ $application->job->title }}</a>
                            </h5>
                            @php $classes = ['pending' => 'bg-warning-soft text-warning', 'accepted' => 'bg-success-soft text-success', 'rejected' => 'bg-danger-soft text-danger']; @endphp
                            <span class="badge {{ $classes[$application->status] }} badge-soft">{{ $application->statusLabel() }}</span>
                        </div>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-tag me-1"></i>{{ $application->job->category }}
                            <span class="mx-1">·</span>
                            <i class="bi bi-geo-alt me-1"></i>{{ $application->job->location }}
                        </p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between small text-muted mb-3">
                                <span><i class="bi bi-calendar-check me-1"></i>Applied {{ $application->created_at->format('d M Y') }}</span>
                                <span class="{{ $application->job->deadline < today() ? 'text-danger' : '' }}">
                                    <i class="bi bi-calendar-event me-1"></i>{{ $application->job->deadline->format('d M Y') }}
                                </span>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('jobs.show', $application->job) }}" class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-eye me-1"></i>View</a>
                                <form method="POST" action="{{ route('applications.destroy', $application->job) }}" data-confirm="Are you sure you want to cancel this application? Your seat will be released." data-confirm-title="Cancel application?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-circle me-1"></i>Cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $applications->links() }}
    </div>
@endif
@endsection
