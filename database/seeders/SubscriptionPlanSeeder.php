<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'max_events' => 1,
                'max_registrations' => 100,
                'features' => json_encode(['basic_support']),
                'is_active' => true,
            ],
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price_monthly' => 29,
                'price_yearly' => 290,
                'max_events' => 10,
                'max_registrations' => 2000,
                'features' => json_encode(['email_support', 'custom_branding']),
                'is_active' => true,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'price_monthly' => 99,
                'price_yearly' => 990,
                'max_events' => null,
                'max_registrations' => null,
                'features' => json_encode(['priority_support', 'custom_branding', 'api_access']),
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price_monthly' => 299,
                'price_yearly' => 2990,
                'max_events' => null,
                'max_registrations' => null,
                'features' => json_encode(['white_label', 'custom_domain', 'api_access', 'dynamic_qr', 'offline_sync', 'multiple_orgs']),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            DB::table('subscription_plans')->updateOrInsert(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
