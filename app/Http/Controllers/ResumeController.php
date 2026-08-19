<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResumeController extends Controller
{
    private function authorizeAccess(User $user): void
    {
        if (auth()->id() !== $user->id && ! auth()->user()?->isAdmin()) {
            abort(403);
        }
    }

    private function filename(User $user): string
    {
        return 'CV-' . preg_replace('/[^A-Za-z0-9]+/', '-', $user->name)
            . '.' . strtolower(pathinfo($user->resume, PATHINFO_EXTENSION));
    }

    public function show(User $user): StreamedResponse
    {
        $this->authorizeAccess($user);
        abort_unless($user->resume, 404);

        return Storage::disk('public')->response($user->resume, $this->filename($user), [
            'Content-Disposition' => 'inline; filename="' . $this->filename($user) . '"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function download(User $user): StreamedResponse
    {
        $this->authorizeAccess($user);
        abort_unless($user->resume, 404);

        return Storage::disk('public')->download($user->resume, $this->filename($user), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
