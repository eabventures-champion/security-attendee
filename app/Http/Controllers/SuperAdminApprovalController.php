<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Organization;
use App\Mail\OrgAdminActivatedNotification;
use Illuminate\Support\Facades\Mail;

class SuperAdminApprovalController extends Controller
{
    public function approveOrg(string $token)
    {
        $user = User::where('approval_token', $token)->first();

        if (!$user) {
            return view('super-admin.approval-result', [
                'success' => false,
                'message' => 'Invalid or expired approval token.',
            ]);
        }

        // Approve user
        $user->approval_status = 'approved';
        $user->is_active = true;
        $user->approved_at = now();
        $user->save();

        // Approve organization
        if ($user->organization_id) {
            $org = Organization::find($user->organization_id);
            if ($org) {
                $org->approval_status = 'approved';
                $org->is_active = true;
                $org->approved_at = now();
                $org->save();
            }
        }

        // Send activation notification to Organization Admin
        try {
            Mail::to($user->email)->send(new OrgAdminActivatedNotification($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send activation email to {$user->email}: " . $e->getMessage());
        }

        return view('super-admin.approval-result', [
            'success' => true,
            'user' => $user,
            'organization' => $user->organization,
            'message' => "Organization '{$user->organization?->name}' & Admin '{$user->name}' have been approved & activated successfully!",
        ]);
    }
}
