<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists and invitation/approval status
        $user = User::where('email', $credentials['email'])->first();

        $isPrimaryAdmin = $user && ($user->hasRole('super_admin') || $user->hasRole('organization_admin') || $user->email === 'superadmin@attendflow.com');

        if ($user && !$isPrimaryAdmin && $user->invitation_status === 'pending') {
            return back()->withErrors([
                'email' => '✉️ Please accept your team invitation email before logging in. Click the invitation link sent to your email to confirm your account & activate dashboard access.',
            ])->onlyInput('email');
        }

        if ($user && !$isPrimaryAdmin && $user->approval_status === 'pending_approval') {
            return back()->withErrors([
                'email' => '🔒 Your organization registration is awaiting Super Admin review & approval. You will receive an email once your account is activated.',
            ])->onlyInput('email');
        }

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => '⚠️ Your account is inactive or has been suspended. Please contact your Workspace Administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(array_merge($credentials, ['is_active' => true]), $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Update last login
            $request->user()->update(['last_login_at' => now()]);

            // Set organization context for global scopes
            if ($request->user()->organization_id) {
                session(['current_organization_id' => $request->user()->organization_id]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
