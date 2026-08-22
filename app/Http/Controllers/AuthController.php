<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['nullable', Rule::in(['admin', 'candidate'])],
            'age' => ['nullable', 'integer', 'min:16', 'max:99'],
            'job_title' => ['required_unless:role,admin', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'skills' => ['required_unless:role,admin', 'string'],
        ]);

        $data['role'] = $data['role'] ?? 'candidate';

        if ($data['role'] === 'admin') {
            $data = array_intersect_key($data, array_flip(['name', 'email', 'password', 'role']));
        }

        $user = User::create($data);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $request->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
