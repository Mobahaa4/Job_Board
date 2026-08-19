@extends('layouts.app')

@section('title', 'Find Your Dream Job - AI Job Board')

@section('content')
<div class="hero mb-4">
    <div class="hero-content text-center mx-auto" style="max-width: 1000px;">
        <span class="badge bg-white text-primary rounded-pill px-3 py-2 mb-3">
            <i class="bi bi-robot me-1"></i>AI-powered job matching
        </span>
        <h1 class="display-5 fw-bold text-white mb-3">Find your dream job</h1>
        <p class="fs-5 text-white opacity-75 mb-4">Browse open positions from our database and apply in minutes. The AI assistant can even recommend jobs based on your skills.</p>

        <div class="hero-stat d-inline-flex align-items-center gap-2 card border-0 shadow-sm px-4 py-2 mb-4">
            <i class="bi bi-briefcase-fill text-primary fs-3"></i>
            <div>
                <div class="fw-bold fs-3 text-dark lh-1">{{ $availableJobs }}</div>
                <div class="small text-muted">Available jobs</div>
            </div>
        </div>

        <form method="GET" action="{{ route('jobs.index') }}" id="searchForm" class="hero-search shadow-lg">
            <div class="hero-search-inner">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-lg-4">
                        <div class="search-input-wrap">
                            <i class="bi bi-search text-muted"></i>
                            <input type="text" id="search" name="search" class="form-control" placeholder="Search jobs, skills, or locations..." value="{{ request('search') }}" autocomplete="off">
                            @if (request()->filled('search'))
                                <button type="button" class="search-clear" id="clearSearch" aria-label="Clear search"><i class="bi bi-x-circle-fill"></i></button>
                            @endif
                        </div>
                    </div>
                    <div class="col-6 col-lg-2">
                        <div class="select-wrap">
                            <select id="category" class="form-select" name="category" aria-label="Category">
                                <option value="">All categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="select-wrap">
                            <select id="work_type" class="form-select" name="work_type" aria-label="Work type">
                                <option value="">All work types</option>
                                <option value="remote" @selected(request('work_type') === 'remote')>Remote</option>
                                <option value="on-site" @selected(request('work_type') === 'on-site')>On-site</option>
                                <option value="hybrid" @selected(request('work_type') === 'hybrid')>Hybrid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="select-wrap">
                            <select id="location" class="form-select" name="location" aria-label="Location">
                                <option value="">All locations</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @php $hasActiveFilters = request()->filled('search') || request()->filled('category') || request()->filled('work_type') || request()->filled('location'); @endphp
            <div class="hero-search-footer">
                <span class="small text-muted">
                    @if ($hasActiveFilters)
                        <i class="bi bi-funnel me-1"></i>Filters active
                    @else
                        <i class="bi bi-briefcase me-1"></i>All jobs
                    @endif
                </span>
                <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary btn-clear-filters rounded-pill {{ $hasActiveFilters ? '' : 'disabled' }}" aria-disabled="{{ $hasActiveFilters ? 'false' : 'true' }}" id="clearAllFilters">
                    <i class="bi bi-x-circle me-1"></i>Clear all filters
                </a>
            </div>
        </form>
    </div>
</div>

<div id="jobs-grid">
    @include('jobs._grid')
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var grid = document.getElementById('jobs-grid');
        var form = document.getElementById('searchForm');
        var input = document.getElementById('search');
        var clear = document.getElementById('clearSearch');
        if (!form || !input || !grid) return;

        var timer = null;
        var loading = false;

        function buildUrl() {
            var fd = new FormData(form);
            var params = new URLSearchParams();
            fd.forEach(function (val, key) { if (val) params.set(key, val); });
            var qs = params.toString();
            return form.action + (qs ? '?' + qs : '');
        }

        function loadJobs(url, push) {
            if (loading) return;
            loading = true;
            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    grid.innerHTML = data.html;
                    loading = false;
                    if (push !== false) history.pushState({}, '', url);
                    bindPagination();
                })
                .catch(function () { loading = false; });
        }

        function bindPagination() {
            var links = grid.querySelectorAll('#jobs-pagination a');
            for (var i = 0; i < links.length; i++) {
                links[i].addEventListener('click', function (e) {
                    e.preventDefault();
                    loadJobs(this.href);
                });
            }
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            clearTimeout(timer);
            loadJobs(buildUrl());
        });

        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                loadJobs(buildUrl());
            }, 400);
        });

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(timer);
                loadJobs(buildUrl());
            }
        });

        var selects = form.querySelectorAll('select');
        for (var i = 0; i < selects.length; i++) {
            selects[i].addEventListener('change', function () {
                clearTimeout(timer);
                loadJobs(buildUrl());
            });
        }

        if (clear) {
            clear.addEventListener('click', function () {
                input.value = '';
                var selects = form.querySelectorAll('select');
                for (var i = 0; i < selects.length; i++) selects[i].value = '';
                loadJobs(form.action);
            });
        }

        var clearAll = document.getElementById('clearAllFilters');
        if (clearAll) {
            clearAll.addEventListener('click', function (e) {
                e.preventDefault();
                input.value = '';
                var selects = form.querySelectorAll('select');
                for (var i = 0; i < selects.length; i++) selects[i].value = '';
                loadJobs(form.action);
            });
        }

        window.addEventListener('popstate', function () {
            loadJobs(location.href, false);
        });

        bindPagination();
    })();
</script>
@endpush
