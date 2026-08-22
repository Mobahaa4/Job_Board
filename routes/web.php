<?php

use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ApplicationCvController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResumeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [JobController::class, 'index'])->name('home');

Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/image', [ProfileController::class, 'destroyImage'])->name('profile.image.destroy');
    Route::delete('/profile/resume', [ProfileController::class, 'destroyResume'])->name('profile.resume.destroy');

    Route::get('/resume/{user}/view', [ResumeController::class, 'show'])->name('resume.show');
    Route::get('/resume/{user}/download', [ResumeController::class, 'download'])->name('resume.download');

    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}/cv/view', [ApplicationCvController::class, 'show'])->name('applications.cv.show');
    Route::get('/applications/{application}/cv/download', [ApplicationCvController::class, 'download'])->name('applications.cv.download');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::delete('/jobs/{job}/apply', [ApplicationController::class, 'destroy'])->name('applications.destroy');

    Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot/send', [ChatbotController::class, 'send'])->name('chatbot.send');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [AdminJobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [AdminJobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}/edit', [AdminJobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [AdminJobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [AdminJobController::class, 'destroy'])->name('jobs.destroy');

    Route::get('/candidates', [CandidateController::class, 'index'])->name('candidates.index');

    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::put('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.update-status');
});
