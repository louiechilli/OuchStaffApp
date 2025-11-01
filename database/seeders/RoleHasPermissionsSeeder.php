<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleHasPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder assigns permissions to roles based on the role_has_permissions
     * relationships found in the database dump.
     */
    public function run(): void
    {
        // Get roles
        $inventoryRole = Role::firstOrCreate(['name' => 'inventory'], ['guard_name' => 'web']);
        $artistRole = Role::firstOrCreate(['name' => 'artist'], ['guard_name' => 'web']);

        // Get permissions
        $permissions = [
            'view-inventory' => Permission::firstOrCreate(['name' => 'view-inventory'], ['guard_name' => 'web']),
            'view-messages' => Permission::firstOrCreate(['name' => 'view-messages'], ['guard_name' => 'web']),
            'view-bookings' => Permission::firstOrCreate(['name' => 'view-bookings'], ['guard_name' => 'web']),
            'view-clients' => Permission::firstOrCreate(['name' => 'view-clients'], ['guard_name' => 'web']),
            'view-revenue' => Permission::firstOrCreate(['name' => 'view-revenue'], ['guard_name' => 'web']),
            'view-calendar' => Permission::firstOrCreate(['name' => 'view-calendar'], ['guard_name' => 'web']),
            'create-booking' => Permission::firstOrCreate(['name' => 'create-booking'], ['guard_name' => 'web']),
            'create-sale' => Permission::firstOrCreate(['name' => 'create-sale'], ['guard_name' => 'web']),
            'send-message' => Permission::firstOrCreate(['name' => 'send-message'], ['guard_name' => 'web']),
            'view-reports' => Permission::firstOrCreate(['name' => 'view-reports'], ['guard_name' => 'web']),
            'admin' => Permission::firstOrCreate(['name' => 'admin'], ['guard_name' => 'web']),
        ];

        // Assign permissions to inventory role (has all permissions)
        $inventoryRole->syncPermissions([
            $permissions['view-inventory'],
            $permissions['view-messages'],
            $permissions['view-bookings'],
            $permissions['view-clients'],
            $permissions['view-revenue'],
            $permissions['view-calendar'],
            $permissions['create-booking'],
            $permissions['create-sale'],
            $permissions['send-message'],
            $permissions['view-reports'],
            $permissions['admin'],
        ]);

        // Assign permissions to artist role
        $artistRole->syncPermissions([
            $permissions['view-messages'],
            $permissions['view-bookings'],
            $permissions['view-clients'],
            $permissions['view-calendar'],
            $permissions['create-booking'],
            $permissions['create-sale'],
            $permissions['send-message'],
        ]);

        $this->command->info('Role permissions assigned successfully!');
        $this->command->info("Inventory role has " . $inventoryRole->permissions()->count() . " permissions");
        $this->command->info("Artist role has " . $artistRole->permissions()->count() . " permissions");
    }
}

