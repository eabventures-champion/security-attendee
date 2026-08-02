<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;

class AcceptInvitationController extends Controller
{
    public function accept(string $token)
    {
        $user = User::where('approval_token', $token)
            ->orWhere('invitation_token', $token)
            ->first();

        if (!$user) {
            return view('auth.invitation-accepted', [
                'success' => false,
                'message' => 'Invalid or expired confirmation link.',
            ]);
        }

        // Mark receipt as confirmed
        $user->invitation_status = 'confirmed';
        $user->is_active = true;
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
        }
        $user->save();

        if ($user->organization_id) {
            $org = Organization::find($user->organization_id);
            if ($org) {
                $org->is_active = true;
                $org->approval_status = 'approved';
                $org->save();
            }
        }

        return view('auth.invitation-accepted', [
            'success' => true,
            'user' => $user,
            'organization' => $user->organization,
            'message' => "Receipt Confirmed! Your workspace invitation for '{$user->organization?->name}' has been accepted and activated.",
        ]);
    }
}
