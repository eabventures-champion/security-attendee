<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'organization_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'terms' => ['required', 'accepted'],
        ]);

        // Create organization
        $organization = Organization::create([
            'name' => $validated['organization_name'],
            'slug' => Str::slug($validated['organization_name']) . '-' . Str::random(4),
            'brand_color' => '#3b82f6',
            'is_active' => true,
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'organization_id' => $organization->id,
            'is_active' => true,
        ]);

        // Assign organization admin role (if Spatie is configured)
        try {
            $user->assignRole('organization_admin');
        } catch (\Exception $e) {
            // Role might not be seeded yet
        }

        Auth::login($user);

        // Set organization context for global scopes
        session(['current_organization_id' => $organization->id]);

        return redirect()->route('dashboard');
    }
}
