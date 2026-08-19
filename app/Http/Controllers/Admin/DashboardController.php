<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'jobs' => Job::count(),
            'openJobs' => Job::where('deadline', '>=', today())->count(),
            'candidates' => User::where('role', 'candidate')->count(),
            'applications' => Application::count(),
        ];

        $recentApplications = Application::with(['user', 'job'])->latest()->take(6)->get();
        $recentJobs = Job::latest()->take(6)->get();

        return view('admin.dashboard', compact('stats', 'recentApplications', 'recentJobs'));
    }
}
