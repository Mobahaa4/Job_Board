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
                            <p class="text-muted small mb-0">Ask "How many jobs are open?" or "How many candidates are registered?" for live stats.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
