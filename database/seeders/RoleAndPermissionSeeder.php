<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Organizations
            'organizations.view', 'organizations.create', 'organizations.update', 'organizations.delete', 'organizations.manage_members', 'organizations.manage_settings',
            // Events
            'events.view', 'events.create', 'events.update', 'events.delete', 'events.publish', 'events.archive', 'events.cancel', 'events.duplicate', 'events.manage_attendees', 'events.manage_gates', 'events.view_reports',
            // Attendees
            'attendees.view', 'attendees.create', 'attendees.update', 'attendees.delete', 'attendees.verify', 'attendees.check_in', 'attendees.download_qr',
            // Gates
            'gates.view', 'gates.create', 'gates.update', 'gates.delete', 'gates.assign_roles',
            // Reports
            'reports.view', 'reports.export',
            // Scanner
            'scanner.scan', 'scanner.manual_checkin', 'scanner.manual_search',
            // Audit
            'audit.view'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        $orgAdmin = Role::firstOrCreate(['name' => 'organization_admin']);
        $orgAdmin->givePermissionTo(Permission::where('name', '!=', 'audit.view')->get());

        $eventManager = Role::firstOrCreate(['name' => 'event_manager']);
        $eventManager->givePermissionTo([
            'events.view', 'events.create', 'events.update', 'events.delete', 'events.publish', 'events.archive', 'events.cancel', 'events.duplicate', 'events.manage_attendees', 'events.manage_gates', 'events.view_reports',
            'attendees.view', 'attendees.create', 'attendees.update', 'attendees.delete', 'attendees.verify', 'attendees.check_in', 'attendees.download_qr',
            'gates.view', 'gates.create', 'gates.update', 'gates.delete', 'gates.assign_roles',
            'reports.view', 'reports.export'
        ]);

        $securityOfficer = Role::firstOrCreate(['name' => 'security_officer']);
        $securityOfficer->givePermissionTo([
            'scanner.scan', 'scanner.manual_checkin', 'scanner.manual_search',
            'attendees.view', 'attendees.check_in',
            'gates.view'
        ]);

        $volunteer = Role::firstOrCreate(['name' => 'volunteer']);
        $volunteer->givePermissionTo([
            'events.view', 'attendees.view', 'scanner.scan'
        ]);

        $scannerOperator = Role::firstOrCreate(['name' => 'scanner_operator']);
        $scannerOperator->givePermissionTo(['scanner.scan']);

        $attendee = Role::firstOrCreate(['name' => 'attendee']);
        // Attendee has no permissions by default
    }
}
