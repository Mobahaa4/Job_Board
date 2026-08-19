@extends('admin.layouts.admin')

@section('title', 'Manage Jobs - AI Job Board')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Jobs</h3>
        <p class="text-muted mb-0">Add, edit and delete job postings.</p>
    </div>
    <a href="{{ route('admin.jobs.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add New Job</a>
</div>

<form method="GET" action="{{ route('admin.jobs.index') }}" class="mb-3">
    <div class="input-group" style="max-width: 380px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Search by title or category..." value="{{ request('search') }}">
        <button class="btn btn-primary">Search</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        @if ($jobs->isEmpty())
            <div class="text-center text-muted py-5">No jobs found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Work Type</th>
                            <th>Salary</th>
                            <th>Deadline</th>
                            <th>Applications</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs as $job)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $job->title }}</div>
                                    @if ($job->deadline < today())
                                        <span class="badge bg-danger-soft badge-soft mt-1">Closed</span>
                                    @else
                                        <span class="badge bg-success-soft badge-soft mt-1">Open</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary-soft badge-soft">{{ $job->category }}</span></td>
                                <td class="text-muted">{{ $job->location }}</td>
                                <td>{{ $job->workTypeLabel() }}</td>
                                <td>{{ $job->salary ? '$' . number_format($job->salary, 0) : '—' }}</td>
                                <td class="{{ $job->deadline < today() ? 'text-danger' : '' }}">{{ $job->deadline->format('d M Y') }}</td>
                                <td><span class="badge bg-light text-dark border badge-soft">{{ $job->applications_count }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('jobs.show', $job) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.jobs.edit', $job) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.jobs.destroy', $job) }}" class="d-inline" data-confirm="This job and all of its applications will be permanently deleted." data-confirm-title="Delete this job?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $jobs->links() }}
</div>
@endsection
