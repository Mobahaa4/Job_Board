@extends('admin.layouts.admin')

@section('title', 'Edit Job - AI Job Board')

@section('admin-content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Edit Job: {{ $job->title }}</h3>
    <a href="{{ route('admin.jobs.index') }}" class="btn btn-outline-secondary btn-sm">Back to Jobs</a>
</div>

<div class="card">
    <div class="card-body p-4">
        @include('admin.jobs._form', [
            'action' => route('admin.jobs.update', $job),
            'method' => 'PUT',
            'job' => $job,
            'button' => 'Update Job',
        ])
    </div>
</div>
@endsection
