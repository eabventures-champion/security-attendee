<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(int $userId)
    {
        $currentUser = Auth::user();

        // Only Super Admin can initiate impersonation
        if (!$currentUser->hasRole('super_admin') && $currentUser->email !== 'superadmin@attendflow.com') {
            return back()->with('error', 'Unauthorized action.');
        }

        $targetUser = User::findOrFail($userId);

        // Store original Super Admin ID in session
        session([
            'impersonator_id' => $currentUser->id,
            'impersonator_name' => $currentUser->name,
        ]);

        // Login as target Organization Admin
        Auth::login($targetUser);

        if ($targetUser->organization_id) {
            session(['current_organization_id' => $targetUser->organization_id]);
        }

        return redirect()->route('dashboard')->with('message', "⚡ Now impersonating workspace for {$targetUser->organization?->name} ({$targetUser->name}).");
    }

    public function stopImpersonating()
    {
        $impersonatorId = session('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        $superAdmin = User::findOrFail($impersonatorId);

        session()->forget(['impersonator_id', 'impersonator_name']);
        Auth::login($superAdmin);

        return redirect()->route('users.index')->with('message', 'Returned to Super Admin Dashboard.');
    }
}
