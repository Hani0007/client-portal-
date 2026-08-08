<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cached roles/permissions
        Artisan::call('permission:cache-reset');

        // ----- Create Permissions -----
        $permissions = [
            // Agency owner permissions
            'manage-agency',
            'create-project',
            'edit-project',
            'delete-project',
            'upload-deliverable',
            'send-invoice',
            'view-all-projects',

            // Shared permissions
            'send-message',
            'view-project',

            // Client permissions
            'approve-deliverable',
            'request-changes',
            'pay-invoice',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----- Create Roles -----
        $agencyOwner = Role::firstOrCreate(['name' => 'agency_owner', 'guard_name' => 'web']);
        $client      = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);

        // ----- Assign Permissions to Roles -----

        // Agency owner gets everything
        $agencyOwner->givePermissionTo([
            'manage-agency',
            'create-project',
            'edit-project',
            'delete-project',
            'upload-deliverable',
            'send-invoice',
            'view-all-projects',
            'send-message',
            'view-project',
        ]);

        // Client gets client-side actions only
        $client->givePermissionTo([
            'approve-deliverable',
            'request-changes',
            'pay-invoice',
            'send-message',
            'view-project',
        ]);
    }
}