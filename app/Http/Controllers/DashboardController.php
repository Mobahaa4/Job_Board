<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $stats = [
            'applications' => $user->applications()->count(),
            'openJobs' => Job::where('deadline', '>=', today())->count(),
            'pending' => $user->applications()->where('status', 'pending')->count(),
        ];

        $recentApplications = $user->applications()
            ->with('job')
            ->latest()
            ->take(5)
            ->get();

        $recommended = null;
        if ($user->skills || $user->job_title) {
            $recommended = Job::where('deadline', '>=', today())
                ->get()
                ->filter(fn (Job $job) => $this->score($job, $user) > 0)
                ->sortByDesc(fn (Job $job) => $this->score($job, $user))
                ->take(5);
        }

        return view('dashboard', compact('stats', 'recentApplications', 'recommended'));
    }

    private function score(Job $job, $user): int
    {
        $profileSkills = $user->skillsList();
        $jobSkills = $job->requiredSkillsList();
        $score = count(array_intersect($profileSkills, $jobSkills));

        if ($user->job_title && str_contains(strtolower($job->title), strtolower($user->job_title))) {
            $score += 3;
        }

        return $score;
    }
}
