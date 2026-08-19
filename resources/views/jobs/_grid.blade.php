<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h3 class="section-title mb-0"><i class="bi bi-lightning-charge-fill text-warning me-1"></i>Available Jobs</h3>
    <span class="text-muted small">{{ $jobs->total() }} job(s) found</span>
</div>

@if ($jobs->isEmpty())
    <div class="text-center text-muted py-5">
        <i class="bi bi-inbox display-4 d-block mb-2"></i>
        No jobs found matching your filters.
        <a href="{{ route('jobs.index') }}" class="d-block mt-2">Clear filters</a>
    </div>
@else
    <div class="row g-3">
        @foreach ($jobs as $job)
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('jobs.show', $job) }}" class="text-decoration-none">
                    <div class="card job-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start gap-3 mb-3">
                                <span class="job-icon"><i class="bi bi-briefcase"></i></span>
                                <div class="flex-grow-1">
                                    <h5 class="fw-bold text-dark mb-1">{{ $job->title }}</h5>
                                    <div class="text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $job->location }}
                                        <span class="mx-1">·</span>
                                        <i class="bi bi-tag me-1"></i>{{ $job->category }}
                                    </div>
                                </div>
                                @if ($job->deadline < today())
                                    <span class="badge bg-danger-soft badge-soft">Closed</span>
                                @else
                                    <span class="badge bg-success-soft badge-soft">Open</span>
                                @endif
                            </div>
                            <p class="text-muted small mb-3">{{ Str::limit($job->description, 110) }}</p>
                            <div class="d-flex flex-wrap gap-1 mb-3">
                                @foreach (array_slice($job->requiredSkillsList(), 0, 3) as $skill)
                                    <span class="badge bg-primary-soft badge-soft">{{ ucwords($skill) }}</span>
                                @endforeach
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="fw-semibold text-success small">
                                    @if ($job->salary) ${{ number_format($job->salary, 0) }}/yr @else Salary on request @endif
                                </span>
                                <div class="text-end">
                                    <div class="small text-muted"><i class="bi bi-person me-1"></i>{{ $job->applications_count }} applicant(s)</div>
                                    @if ($job->deadline >= today())
                                        <div class="small text-muted">Deadline: {{ $job->deadline->format('d M') }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4" id="jobs-pagination">
        {{ $jobs->links() }}
    </div>
@endif
