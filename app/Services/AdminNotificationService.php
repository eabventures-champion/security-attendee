<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminNotificationService
{
    /**
     * Send in-app notification to organization admins.
     */
    public static function send($organizationId, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        $admins = User::where('organization_id', $organizationId)->get();
        if ($admins->isEmpty()) {
            $admins = User::all();
        }

        // Exclude security personnel from receiving registration/admin alerts
        $admins = $admins->reject(fn($user) => $user->isSecurityPersonnel());

        foreach ($admins as $admin) {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\AdminAlertNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'link' => $link,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Send in-app notification specifically to Super Admins.
     */
    public static function sendSuperAdmin(string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        $superAdmins = User::whereHas('roles', fn($r) => $r->where('name', 'super_admin'))
            ->orWhere('email', 'superadmin@attendflow.com')
            ->get();

        foreach ($superAdmins as $admin) {
            DB::table('notifications')->insert([
                'id' => (string) Str::uuid(),
                'type' => 'App\Notifications\AdminAlertNotification',
                'notifiable_type' => 'App\Models\User',
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'link' => $link,
                ]),
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
