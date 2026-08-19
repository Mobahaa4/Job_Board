<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $query = Job::query()->withCount('applications');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('required_skills', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->string('work_type'));
        }

        if ($request->filled('location')) {
            $query->where('location', $request->string('location'));
        }

        $query->where('deadline', '>=', today());

        $jobs = $query->latest()->paginate(9)->withQueryString();
        $categories = Job::distinct()->pluck('category')->sort()->values();
        $locations = Job::distinct()->pluck('location')->sort()->values();
        $availableJobs = Job::where('deadline', '>=', today())->count();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('jobs._grid', compact('jobs'))->render(),
            ]);
        }

        return view('jobs.index', compact('jobs', 'categories', 'locations', 'availableJobs'));
    }

    public function show(Job $job): View
    {
        $application = auth()->user()?->applications()->where('job_listing_id', $job->id)->first();

        return view('jobs.show', compact('job', 'application'));
    }
}
