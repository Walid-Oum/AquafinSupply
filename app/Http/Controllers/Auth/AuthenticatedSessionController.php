<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
   public function store(LoginRequest $request): RedirectResponse
{
    $request->authenticate();

    $request->session()->regenerate();

    $user = Auth::user();

    // Check of de gebruiker actief is
    if (!$user->is_active) {
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'email' => 'Je account is gedeactiveerd. Neem contact op met de administrator.',
        ]);
    }

    if ($user->role === 'technieker') {
        return redirect()->route('dashboard');
    }

    if ($user->role === 'magazijn') {
        return redirect()->route('dashboard');
    }

    if ($user->role === 'admin') {
        return redirect()->route('dashboard');
    }

    return redirect('/');
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