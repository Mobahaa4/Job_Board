@extends('admin.layouts.admin')

@section('title', 'Applications - AI Job Board')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Job Applications</h3>
        <p class="text-muted mb-0">All applications submitted by candidates.</p>
    </div>
</div>

<form method="GET" action="{{ route('admin.applications.index') }}" class="mb-3">
    <select name="status" class="form-select" style="max-width: 220px;" onchange="this.form.submit()">
        <option value="">All statuses</option>
        <option value="pending" @selected(request('status') === 'pending')>Pending</option>
        <option value="accepted" @selected(request('status') === 'accepted')>Accepted</option>
        <option value="rejected" @selected(request('status') === 'rejected')>Rejected</option>
    </select>
</form>

<div class="card">
    <div class="card-body p-0">
        @if ($applications->isEmpty())
            <div class="text-center text-muted py-5">No applications found.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Job</th>
                            <th>Applied On</th>
                            <th>Cover Letter</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($application->user->image)
                                            <img src="{{ asset('storage/' . $application->user->image) }}" alt="" class="avatar" style="width:34px;height:34px;">
                                        @else
                                            <span class="avatar-initial" style="width:34px;height:34px;font-size:.75rem;">{{ strtoupper(substr($application->user->name, 0, 1)) }}</span>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $application->user->name }}</div>
                                            <div class="text-muted small">{{ $application->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('jobs.show', $application->job) }}" class="text-decoration-none">{{ $application->job->title }}</a>
                                    <div class="text-muted small">{{ $application->job->location }}</div>
                                </td>
                                <td>{{ $application->created_at->format('d M Y') }}</td>
                                <td>
                                    @if ($application->cover_letter)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#coverModal{{ $application->id }}">View</button>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php $classes = ['pending' => 'bg-warning-soft text-warning', 'accepted' => 'bg-success-soft text-success', 'rejected' => 'bg-danger-soft text-danger']; @endphp
                                    <span class="badge {{ $classes[$application->status] }} badge-soft">{{ $application->statusLabel() }}</span>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.applications.update-status', $application) }}" class="d-flex justify-content-end gap-1">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" class="form-select form-select-sm" style="width: auto;">
                                            @foreach (['pending', 'accepted', 'rejected'] as $status)
                                                <option value="{{ $status }}" @selected($application->status === $status)>{{ ucfirst($status) }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-sm btn-primary">Update</button>
                                    </form>
                                </td>
                            </tr>

                            @if ($application->cover_letter)
                                <div class="modal fade" id="coverModal{{ $application->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cover Letter — {{ $application->user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body" style="white-space: pre-line;">{{ $application->cover_letter }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $applications->links() }}
</div>
@endsection
