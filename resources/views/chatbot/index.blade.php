@extends('layouts.app')

@section('title', 'AI Assistant - AI Job Board')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">
        <div class="text-center mb-4">
            <span class="brand-icon mb-3" style="width:64px;height:64px;font-size:2rem;"><i class="bi bi-robot"></i></span>
            <h3 class="fw-bold mb-1">Meet Jobot, your AI assistant</h3>
            <p class="text-muted mb-0">
                Jobot answers your questions using the jobs and data in this website's database, powered by an AI model.
            </p>
        </div>

        @include('partials.chat-widget', ['embedded' => true, 'history' => $history ?? collect()])

        @auth
            @if (auth()->user()->isAdmin())
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-primary-soft text-primary"><i class="bi bi-people"></i></span>
                                <div>
                                    <div class="fw-semibold">Candidates</div>
                                    <p class="text-muted small mb-0">Ask "List all candidates" to see registered users and their skills.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-success-soft text-success"><i class="bi bi-file-earmark-text"></i></span>
                                <div>
                                    <div class="fw-semibold">Applications</div>
                                    <p class="text-muted small mb-0">Ask "Show all applications" to review who applied where.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-warning-soft text-warning"><i class="bi bi-graph-up"></i></span>
                                <div>
                                    <div class="fw-semibold">Statistics</div>
                                    <p class="text-muted small mb-0">Ask "How many candidates are registered?" for live platform stats.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-danger-soft text-danger"><i class="bi bi-plus-circle"></i></span>
                                <div>
                                    <div class="fw-semibold">Manage jobs</div>
                                    <p class="text-muted small mb-0">Ask "How do I add a new job?" or "How do I delete a job?"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-primary-soft text-primary"><i class="bi bi-stars"></i></span>
                                <div>
                                    <div class="fw-semibold">Job recommendations</div>
                                    <p class="text-muted small mb-0">Ask "What are the best jobs for me?" to get matches based on your profile skills.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-success-soft text-success"><i class="bi bi-search"></i></span>
                                <div>
                                    <div class="fw-semibold">Browse & filter</div>
                                    <p class="text-muted small mb-0">Ask "Show me remote jobs" or "List IT jobs" to filter by category, location or work type.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 p-3">
                            <div class="d-flex gap-2">
                                <span class="stat-icon bg-warning-soft text-warning"><i class="bi bi-graph-up"></i></span>
                                <div>
                                    <div class="fw-semibold">Platform statistics</div>
                                    <p class="text-muted small mb-0">Ask "How many jobs are open?" for live stats.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="row g-3 mt-2">
                <div class="col-md-4">
                    <div class="card h-100 p-3">
                        <div class="d-flex gap-2">
                            <span class="stat-icon bg-primary-soft text-primary"><i class="bi bi-stars"></i></span>
                            <div>
                                <div class="fw-semibold">Browse jobs</div>
                                <p class="text-muted small mb-0">Ask "What are the best jobs?" to see available openings.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-3">
                        <div class="d-flex gap-2">
                            <span class="stat-icon bg-success-soft text-success"><i class="bi bi-search"></i></span>
                            <div>
                                <div class="fw-semibold">Filter by type</div>
                                <p class="text-muted small mb-0">Ask "Show me remote jobs" to filter by category, location or work type.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 p-3">
                        <div class="d-flex gap-2">
                            <span class="stat-icon bg-warning-soft text-warning"><i class="bi bi-graph-up"></i></span>
                            <div>
                                <div class="fw-semibold">Platform statistics</div>
                                <p class="text-muted small mb-0">Ask "How many jobs are open?" for live stats.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endauth
    </div>
</div>
@endsection
