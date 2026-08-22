<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationCvController extends Controller
{
    private function authorizeAccess(Application $application): void
    {
        if (auth()->id() !== $application->user_id && ! auth()->user()?->isAdmin()) {
            abort(403);
        }
    }

    private function filename(Application $application): string
    {
        return 'CV-' . preg_replace('/[^A-Za-z0-9]+/', '-', $application->user->name)
            . '-' . $application->job->id
            . '.' . strtolower(pathinfo($application->cv_path, PATHINFO_EXTENSION));
    }

    public function show(Application $application): StreamedResponse
    {
        $this->authorizeAccess($application);
        abort_unless($application->cv_path, 404);

        return Storage::disk('public')->response($application->cv_path, $this->filename($application), [
            'Content-Disposition' => 'inline; filename="' . $this->filename($application) . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(Application $application): StreamedResponse
    {
        $this->authorizeAccess($application);
        abort_unless($application->cv_path, 404);

        return Storage::disk('public')->download($application->cv_path, $this->filename($application), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
