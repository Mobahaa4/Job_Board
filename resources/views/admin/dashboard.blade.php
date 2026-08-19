@extends('admin.layouts.admin')

@section('title', 'Admin Dashboard - AI Job Board')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Admin Dashboard</h3>
        <p class="text-muted mb-0">Overview of jobs, candidates and applications.</p>
    </div>
    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add New Job</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-primary-soft text-primary"><i class="bi bi-briefcase"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['jobs'] }}</div>
                <div class="text-muted small">Total Jobs</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-success-soft text-success"><i class="bi bi-briefcase-fill"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['openJobs'] }}</div>
                <div class="text-muted small">Open Jobs</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-warning-soft text-warning"><i class="bi bi-people"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['candidates'] }}</div>
                <div class="text-muted small">Candidates</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card p-3 d-flex flex-row align-items-center gap-3">
            <span class="stat-icon bg-danger-soft text-danger"><i class="bi bi-envelope"></i></span>
            <div>
                <div class="fs-3 fw-bold">{{ $stats['applications'] }}</div>
                <div class="text-muted small">Applications</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Recent Applications</span>
                <a href="{{ route('admin.applications.index') }}" class="small text-decoration-none">View all</a>
            </div>
            <div class="card-body p-0">
                @if ($recentApplications->isEmpty())
                    <div class="text-center text-muted py-4">No applications yet.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Candidate</th>
                                    <th>Job</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentApplications as $application)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($application->user->image)
                                                    <img src="{{ asset('storage/' . $application->user->image) }}" alt="" class="avatar" style="width:32px;height:32px;">
                                                @else
                                                    <span class="avatar-initial" style="width:32px;height:32px;font-size:.75rem;">{{ strtoupper(substr($application->user->name, 0, 1)) }}</span>
                                                @endif
                                                <span>{{ $application->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $application->job->title }}</td>
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
    </div>
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">Latest Jobs</span>
                <a href="{{ route('admin.jobs.create') }}" class="small text-decoration-none">+ Add job</a>
            </div>
            <div class="card-body p-0">
                @if ($recentJobs->isEmpty())
                    <div class="text-center text-muted py-4">No jobs posted yet.</div>
                @else
                    <ul class="list-group list-group-flush">
                        @foreach ($recentJobs as $job)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="job-icon" style="width:34px;height:34px;font-size:.9rem;"><i class="bi bi-briefcase"></i></span>
                                    <div>
                                        <div class="fw-medium">{{ $job->title }}</div>
                                        <small class="text-muted">{{ $job->category }} · {{ $job->location }}</small>
                                    </div>
                                </div>
                                <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
