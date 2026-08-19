<footer class="site-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="footer-brand-icon"><i class="bi bi-briefcase-fill"></i></span>
                    <span class="fw-bold fs-5 text-white">AI Job Board</span>
                </div>
                <p class="mb-0" style="max-width: 320px;">
                    A smart job board where candidates build profiles, apply to jobs, and get personalized
                    recommendations from the AI assistant — all backed by a clean Laravel + MySQL database.
                </p>
            </div>
            <div class="col-6 col-lg-2">
                <h6 class="mb-3">Explore</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('jobs.index') }}">Browse Jobs</a></li>
                    @guest
                        <li><a href="{{ route('register') }}">Register</a></li>
                        <li><a href="{{ route('login') }}">Login</a></li>
                    @endguest
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">For Candidates</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    @auth
                        @if (auth()->user()->isCandidate())
                            <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li><a href="{{ route('applications.index') }}">My Applications</a></li>
                            <li><a href="{{ route('profile.show') }}">My Profile</a></li>
                        @endif
                    @endauth
                    <li><a href="{{ route('chatbot.index') }}">AI Assistant</a></li>
                    <li><a href="{{ route('jobs.index', ['only_open' => 1]) }}">Open Jobs Only</a></li>
                </ul>
            </div>
            <div class="col-6 col-lg-3">
                <h6 class="mb-3">For Admins</h6>
                <ul class="list-unstyled d-grid gap-2 mb-0">
                    @auth
                        @if (auth()->user()->isAdmin())
                            <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                            <li><a href="{{ route('admin.jobs.index') }}">Manage Jobs</a></li>
                            <li><a href="{{ route('admin.jobs.create') }}">Add Job</a></li>
                            <li><a href="{{ route('admin.candidates.index') }}">Candidates</a></li>
                            <li><a href="{{ route('admin.applications.index') }}">Applications</a></li>
                        @else
                            <li class="footer-note small">Login with an admin account to manage jobs, candidates and applications.</li>
                        @endif
                    @else
                        <li class="footer-note small">Login with an admin account to manage jobs, candidates and applications.</li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <span class="small">© {{ date('Y') }} AI Job Board — Laravel Graduation Project</span>
            <span class="small"><i class="bi bi-robot me-1"></i>Powered by Jobot AI</span>
        </div>
    </div>
</footer>
