<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        $subdomain = optional($user->tenant)->subdomain;

        if ($user->hasRole('super_admin')) {
            return redirect()->intended(route('super-admin.dashboard'));
        }

        if (! $subdomain) {
            return redirect()->intended(route('super-admin.dashboard'));
        }

        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard', ['subdomain' => $subdomain]));
        }

        if ($user->hasRole('rider')) {
            return redirect()->intended(route('rider.dashboard', ['subdomain' => $subdomain]));
        }

        return redirect()->intended(route('customer.dashboard', ['subdomain' => $subdomain]));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
