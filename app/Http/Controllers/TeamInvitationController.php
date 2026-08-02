<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class TeamInvitationController extends Controller
{
    public function acceptInvite(string $token)
    {
        $user = User::where('invitation_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Invalid or expired invitation token.');
        }

        // Mark invitation as confirmed
        $user->invitation_status = 'confirmed';
        $user->invitation_accepted_at = now();
        $user->is_active = true;
        $user->save();

        // Log the user in
        Auth::login($user);

        session()->flash('message', "🎉 Welcome aboard, {$user->name}! Your team membership has been confirmed.");

        if ($user->isSecurityPersonnel()) {
            $defaultEventUuid = Event::latest()->value('uuid');
            if ($defaultEventUuid) {
                return redirect()->route('gates.index', ['eventUuid' => $defaultEventUuid]);
            }
        }

        return redirect()->route('dashboard');
    }
}
