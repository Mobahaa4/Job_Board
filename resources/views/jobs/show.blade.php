@extends('layouts.app')

@section('title', $job->title . ' - AI Job Board')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <nav aria-label="breadcrumb" class="mb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('jobs.index') }}">Jobs</a></li>
                <li class="breadcrumb-item active">{{ $job->title }}</li>
            </ol>
        </nav>

        <div class="card mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                    <div class="d-flex align-items-start gap-3">
                        <span class="job-icon" style="width:56px;height:56px;font-size:1.5rem;"><i class="bi bi-briefcase"></i></span>
                        <div>
                            <h2 class="fw-bold mb-1">{{ $job->title }}</h2>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-primary-soft badge-soft">{{ $job->category }}</span>
                                <span class="badge bg-light text-dark border badge-soft"><i class="bi bi-clock me-1"></i>{{ $job->workTypeLabel() }}</span>
                                @if ($job->deadline < today())
                                    <span class="badge bg-danger-soft badge-soft">Closed</span>
                                @else
                                    <span class="badge bg-success-soft badge-soft"><i class="bi bi-check-circle me-1"></i>Open</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Location</div>
                        <div class="fw-medium"><i class="bi bi-geo-alt me-1"></i>{{ $job->location }}</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Salary</div>
                        <div class="fw-medium"><i class="bi bi-currency-dollar me-1"></i>{{ $job->salary ? '$' . number_format($job->salary, 0) : 'On request' }}</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Deadline</div>
                        <div class="fw-medium"><i class="bi bi-calendar-event me-1"></i>{{ $job->deadline->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-muted small">Applications</div>
                        <div class="fw-medium"><i class="bi bi-person me-1"></i>{{ $job->applications_count ?? $job->applications()->count() }}</div>
                    </div>
                </div>

                <h5 class="fw-bold mb-2">Job Description</h5>
                <p class="text-muted mb-4" style="white-space: pre-line;">{{ $job->description }}</p>

                <h5 class="fw-bold mb-2">Required Skills</h5>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach ($job->requiredSkillsList() as $skill)
                        <span class="badge bg-primary-soft badge-soft px-3 py-2">{{ ucwords($skill) }}</span>
                    @endforeach
                </div>

                <div class="border-top pt-4">
                    @auth
                        @if (auth()->user()->isCandidate())
                            @if ($job->deadline < today())
                                <div class="alert alert-secondary mb-0"><i class="bi bi-info-circle me-1"></i>This job has passed its application deadline.</div>
                            @elseif ($application)
                                <div class="alert alert-success mb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <span><i class="bi bi-check-circle-fill me-1"></i>You applied on {{ $application->created_at->format('d M Y') }}.</span>
                                    <form method="POST" action="{{ route('applications.destroy', $job) }}" data-confirm="Cancel your application for this job?" data-confirm-title="Cancel application?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm"><i class="bi bi-x-circle me-1"></i>Cancel Application</button>
                                    </form>
                                </div>
                            @else
                                <h5 class="fw-bold mb-3">Apply for this job</h5>
                                <form method="POST" action="{{ route('applications.store', $job) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="cover_letter" class="form-label fw-medium">Cover Letter (optional)</label>
                                        <textarea class="form-control" id="cover_letter" name="cover_letter" rows="3" placeholder="Tell the employer why you're a good fit..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg px-4"><i class="bi bi-send me-1"></i>Apply Now</button>
                                </form>
                            @endif
                        @else
                            <div class="alert alert-info mb-0"><i class="bi bi-info-circle me-1"></i>You are logged in as an admin. Candidates can apply for this job.</div>
                        @endif
                    @else
                        <div class="d-flex flex-column flex-sm-row align-items-center gap-3 justify-content-between">
                            <p class="mb-0 text-muted">Ready to apply? Create an account or log in to apply in minutes.</p>
                            <div class="d-flex gap-2">
                                <a href="{{ route('login') }}" class="btn btn-outline-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
                                <a href="{{ route('register') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Register</a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
