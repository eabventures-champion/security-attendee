<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\TicketCategory;
use App\Models\EventSession;
use App\Models\Gate;
use App\Models\Attendee;
use App\Models\CheckIn;
use App\Models\QrCode;
use App\Enums\EventStatus;
use App\Enums\VerificationStatus;
use App\Enums\AccessRole;
use App\Enums\ScanResult;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1 Organization
        $org = Organization::firstOrCreate(
            ['slug' => 'techconf-global'],
            [
                'name' => 'TechConf Global',
                'brand_color' => '#3b82f6',
                'description' => 'Leading technology conference organizer',
                'timezone' => 'UTC',
                'is_active' => true,
            ]
        );

        // 1 Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@attendflow.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'organization_id' => $org->id,
                'is_active' => true,
            ]
        );

        try {
            if (!$admin->hasRole('organization_admin')) {
                $admin->assignRole('organization_admin');
            }
        } catch (\Exception $e) {
            // Role might not be available
        }

        // Event 1: Tech Innovation Summit
        $event1 = Event::firstOrCreate(
            ['slug' => 'tech-innovation-summit-2024'],
            [
                'name' => 'Tech Innovation Summit 2024',
                'organization_id' => $org->id,
                'description' => 'The premier technology innovation conference bringing together industry leaders, developers, and entrepreneurs.',
                'venue_name' => 'Grand Convention Center',
                'venue_address' => '123 Innovation Blvd',
                'venue_city' => 'San Francisco',
                'venue_country' => 'US',
                'status' => EventStatus::Published,
                'starts_at' => now()->addDays(30)->setHour(9),
                'ends_at' => now()->addDays(32)->setHour(18),
                'registration_deadline' => now()->addDays(25),
                'capacity' => 500,
                'is_multi_day' => true,
                'is_free' => false,
                'published_at' => now(),
            ]
        );

        // Event 2: Workshop Series
        Event::firstOrCreate(
            ['slug' => 'developer-workshop-series'],
            [
                'name' => 'Developer Workshop Series',
                'organization_id' => $org->id,
                'description' => 'Hands-on workshops for developers of all skill levels.',
                'venue_name' => 'Tech Hub Coworking',
                'venue_city' => 'Austin',
                'venue_country' => 'US',
                'status' => EventStatus::Draft,
                'starts_at' => now()->addDays(60)->setHour(10),
                'ends_at' => now()->addDays(65)->setHour(17),
                'capacity' => 100,
                'is_multi_day' => true,
            ]
        );

        // Event 3: Company Meetup
        Event::firstOrCreate(
            ['slug' => 'annual-company-meetup'],
            [
                'name' => 'Annual Company Meetup',
                'organization_id' => $org->id,
                'description' => 'Our annual company-wide gathering to celebrate achievements and set new goals.',
                'venue_name' => 'Riverside Hotel',
                'venue_city' => 'New York',
                'venue_country' => 'US',
                'status' => EventStatus::Published,
                'starts_at' => now()->addDays(15)->setHour(9),
                'ends_at' => now()->addDays(15)->setHour(20),
                'capacity' => 200,
                'published_at' => now(),
            ]
        );

        // Ticket Categories for Event 1
        TicketCategory::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'VIP'],
            ['price' => 200, 'capacity' => 50, 'access_role' => AccessRole::Vip, 'sort_order' => 1]
        );
        TicketCategory::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'General Admission'],
            ['price' => 50, 'capacity' => 350, 'access_role' => AccessRole::GeneralAdmission, 'sort_order' => 2]
        );
        TicketCategory::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Student'],
            ['price' => 0, 'capacity' => 100, 'access_role' => AccessRole::GeneralAdmission, 'sort_order' => 3]
        );

        // Sessions for Event 1
        EventSession::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Opening Keynote'],
            [
                'description' => 'Keynote address by industry leaders',
                'starts_at' => $event1->starts_at->copy()->addHour(),
                'ends_at' => $event1->starts_at->copy()->addHours(2),
                'room' => 'Main Hall',
                'speaker_name' => 'Dr. Sarah Chen',
                'sort_order' => 1,
            ]
        );
        EventSession::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'AI Workshop'],
            [
                'description' => 'Hands-on AI/ML workshop',
                'starts_at' => $event1->starts_at->copy()->addHours(3),
                'ends_at' => $event1->starts_at->copy()->addHours(5),
                'room' => 'Workshop Room A',
                'speaker_name' => 'James Wilson',
                'capacity' => 50,
                'sort_order' => 2,
            ]
        );
        EventSession::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Panel: Future of Tech'],
            [
                'description' => 'Panel discussion with industry experts',
                'starts_at' => $event1->starts_at->copy()->addHours(6),
                'ends_at' => $event1->starts_at->copy()->addHours(7),
                'room' => 'Main Hall',
                'sort_order' => 3,
            ]
        );

        // Gates for Event 1
        $mainGate = Gate::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Main Entrance'],
            ['allowed_roles' => [AccessRole::GeneralAdmission->value, AccessRole::Vip->value, AccessRole::Speaker->value, AccessRole::Staff->value], 'location' => 'Front Lobby', 'is_active' => true, 'sort_order' => 1]
        );
        Gate::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'VIP Gate'],
            ['allowed_roles' => [AccessRole::Vip->value], 'location' => 'Side Entrance', 'is_active' => true, 'sort_order' => 2]
        );
        Gate::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Speaker Gate'],
            ['allowed_roles' => [AccessRole::Speaker->value, AccessRole::Organizer->value], 'location' => 'Backstage', 'is_active' => true, 'sort_order' => 3]
        );
        Gate::firstOrCreate(
            ['event_id' => $event1->id, 'name' => 'Staff Gate'],
            ['allowed_roles' => [AccessRole::Staff->value, AccessRole::Organizer->value, AccessRole::Security->value], 'location' => 'Service Entrance', 'is_active' => true, 'sort_order' => 4]
        );

        // Attendees for Event 1
        $names = [
            'Emma Johnson', 'Liam Williams', 'Olivia Brown', 'Noah Jones', 'Ava Garcia',
            'Ethan Martinez', 'Sophia Robinson', 'Mason Clark', 'Isabella Rodriguez', 'William Lewis',
            'Mia Lee', 'James Walker', 'Charlotte Hall', 'Benjamin Allen', 'Amelia Young',
            'Lucas Hernandez', 'Harper King', 'Henry Wright', 'Evelyn Lopez', 'Alexander Hill',
            'Abigail Scott', 'Daniel Green', 'Emily Adams', 'Matthew Baker', 'Elizabeth Nelson',
            'Joseph Carter', 'Avery Mitchell', 'Samuel Perez', 'Ella Roberts', 'David Turner',
            'Grace Phillips', 'Carter Campbell', 'Chloe Parker', 'Owen Evans', 'Victoria Edwards',
            'Jack Collins', 'Lily Stewart', 'Luke Sanchez', 'Zoe Morris', 'Gabriel Rogers',
            'Penelope Reed', 'Anthony Cook', 'Layla Morgan', 'Isaac Bell', 'Riley Murphy',
            'Dylan Bailey', 'Nora Rivera', 'Leo Cooper', 'Hazel Richardson', 'Jaxon Cox',
        ];

        if (Attendee::where('event_id', $event1->id)->count() < 50) {
            // 30 Verified
            for ($i = 0; $i < 30; $i++) {
                Attendee::create([
                    'event_id' => $event1->id,
                    'organization_id' => $org->id,
                    'full_name' => $names[$i],
                    'email' => 'attendee' . ($i + 1) . '@example.com',
                    'phone' => '+1' . str_pad(rand(2000000000, 9999999999), 10, '0'),
                    'company' => ['TechCorp', 'InnovateCo', 'DataSystems', 'CloudWorks', 'DevHub'][rand(0, 4)],
                    'job_title' => ['Developer', 'Designer', 'Manager', 'CTO', 'Engineer'][rand(0, 4)],
                    'country' => 'US',
                    'access_role' => $i < 5 ? AccessRole::Vip : AccessRole::GeneralAdmission,
                    'verification_status' => VerificationStatus::Verified,
                    'verified_at' => now()->subDays(rand(1, 10)),
                    'consent' => true,
                ]);
            }

            // 15 Pending
            for ($i = 30; $i < 45; $i++) {
                Attendee::create([
                    'event_id' => $event1->id,
                    'organization_id' => $org->id,
                    'full_name' => $names[$i],
                    'email' => 'attendee' . ($i + 1) . '@example.com',
                    'phone' => '+1' . str_pad(rand(2000000000, 9999999999), 10, '0'),
                    'access_role' => AccessRole::GeneralAdmission,
                    'verification_status' => VerificationStatus::Pending,
                    'verification_token' => Str::random(64),
                    'consent' => true,
                ]);
            }

            // 5 Rejected
            for ($i = 45; $i < 50; $i++) {
                Attendee::create([
                    'event_id' => $event1->id,
                    'organization_id' => $org->id,
                    'full_name' => $names[$i],
                    'email' => 'attendee' . ($i + 1) . '@example.com',
                    'access_role' => AccessRole::GeneralAdmission,
                    'verification_status' => VerificationStatus::Rejected,
                    'consent' => true,
                ]);
            }
        }

        // Create check-ins for 20 verified attendees
        $verifiedAttendees = Attendee::where('event_id', $event1->id)
            ->where('verification_status', VerificationStatus::Verified)
            ->take(20)
            ->get();

        if (CheckIn::where('event_id', $event1->id)->count() < 20) {
            foreach ($verifiedAttendees as $attendee) {
                CheckIn::create([
                    'attendee_id' => $attendee->id,
                    'event_id' => $event1->id,
                    'gate_id' => $mainGate->id,
                    'scanned_by' => $admin->id,
                    'scan_result' => ScanResult::Granted,
                    'scanned_at' => now()->subHours(rand(1, 48)),
                    'ip_address' => '192.168.1.' . rand(1, 254),
                ]);
            }
        }
    }
}
