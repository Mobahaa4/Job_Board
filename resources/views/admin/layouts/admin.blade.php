@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-lg-row gap-4">
    <div class="col-lg-2 flex-shrink-0">
        <div class="card p-3">
            <div class="d-flex align-items-center gap-2 mb-3 px-1">
                <span class="stat-icon bg-gradient text-white" style="width:38px;height:38px;font-size:1rem;"><i class="bi bi-shield-lock"></i></span>
                <h6 class="fw-bold text-muted text-uppercase small mb-0">Admin Panel</h6>
            </div>
            <div class="nav flex-column nav-pills gap-1">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('admin.jobs.*') ? 'active' : '' }}" href="{{ route('admin.jobs.index') }}">
                    <i class="bi bi-briefcase me-2"></i>Jobs
                </a>
                <a class="nav-link {{ request()->routeIs('admin.jobs.create') ? 'active' : '' }}" href="{{ route('admin.jobs.create') }}">
                    <i class="bi bi-plus-circle me-2"></i>Add Job
                </a>
                <a class="nav-link {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}" href="{{ route('admin.candidates.index') }}">
                    <i class="bi bi-people me-2"></i>Candidates
                </a>
                <a class="nav-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}" href="{{ route('admin.applications.index') }}">
                    <i class="bi bi-envelope me-2"></i>Applications
                </a>
            </div>
        </div>
    </div>
    <div class="flex-grow-1">
        @yield('admin-content')
    </div>
</div>
@endsection
