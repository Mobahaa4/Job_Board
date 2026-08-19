<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $applications = Auth::user()->applications()
            ->with('job')
            ->latest()
            ->paginate(10);

        return view('applications.index', compact('applications'));
    }

    public function store(Request $request, Job $job): RedirectResponse
    {
        $user = Auth::user();

        if ($job->deadline < today()) {
            return back()->with('error', 'Sorry, the application deadline for this job has passed.');
        }

        if ($user->applications()->where('job_listing_id', $job->id)->exists()) {
            return back()->with('error', 'You have already applied for this job.');
        }

        $data = $request->validate([
            'cover_letter' => ['nullable', 'string', 'max:5000'],
        ]);

        Application::create([
            'user_id' => $user->id,
            'job_listing_id' => $job->id,
            'cover_letter' => $data['cover_letter'] ?? null,
        ]);

        return back()->with('success', 'Application submitted successfully. Good luck!');
    }

    public function destroy(Job $job): RedirectResponse
    {
        $application = Auth::user()->applications()->where('job_listing_id', $job->id)->first();

        if (! $application) {
            return back()->with('error', 'Application not found.');
        }

        $application->delete();

        return back()->with('success', 'Application cancelled successfully.');
    }
}
