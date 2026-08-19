@extends('admin.layouts.admin')

@section('title', 'Candidates - AI Job Board')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-0">Candidates</h3>
        <p class="text-muted mb-0">All registered candidates.</p>
    </div>
</div>

<form method="GET" action="{{ route('admin.candidates.index') }}" class="mb-3">
    <div class="input-group" style="max-width: 380px;">
        <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Search by name, email or job title..." value="{{ request('search') }}">
        <button class="btn btn-primary">Search</button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        @if ($candidates->isEmpty())
            <div class="text-center text-muted py-5">No candidates registered yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Candidate</th>
                            <th>Job Title</th>
                            <th>Age</th>
                            <th>Phone</th>
                            <th>Skills</th>
                            <th>Applications</th>
                            <th>CV</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($candidates as $candidate)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($candidate->image)
                                            <img src="{{ asset('storage/' . $candidate->image) }}" alt="" class="avatar" style="width:36px;height:36px;">
                                        @else
                                            <span class="avatar-initial" style="width:36px;height:36px;font-size:.8rem;">{{ strtoupper(substr($candidate->name, 0, 1)) }}</span>
                                        @endif
                                        <div>
                                            <div class="fw-medium">{{ $candidate->name }}</div>
                                            <div class="text-muted small">{{ $candidate->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $candidate->job_title ?? '—' }}</td>
                                <td>{{ $candidate->age ?? '—' }}</td>
                                <td>{{ $candidate->phone ?? '—' }}</td>
                                <td>
                                    @if ($candidate->skillsList())
                                        <span class="badge bg-primary-soft badge-soft">{{ count($candidate->skillsList()) }} skill(s)</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><span class="badge bg-light text-dark border badge-soft">{{ $candidate->applications_count }}</span></td>
                                <td>
                                    @if ($candidate->resume)
                                        <a href="{{ route('resume.show', $candidate) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View CV">
                                            <i class="bi bi-file-earmark-pdf me-1"></i>View CV
                                        </a>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>{{ $candidate->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="d-flex justify-content-center mt-4">
    {{ $candidates->links() }}
</div>
@endsection
