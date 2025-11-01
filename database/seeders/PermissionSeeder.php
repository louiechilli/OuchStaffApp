<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates all permissions used throughout the application
     * based on @can directives and permission checks found in the codebase.
     */
    public function run(): void
    {
        $permissions = [
            // Booking permissions
            'create-booking',
            'view-bookings',
            'view-calendar',
            
            // Client permissions
            'create-client',
            'view-clients',
            
            // Sale permissions
            'create-sale',
            
            // Message permissions
            'send-message',
            'view-messages',
            
            // Inventory permissions
            'view-inventory',
            
            // Reporting permissions
            'view-reports',
            'view-revenue',
            
            // Admin permissions
            'admin',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        $this->command->info('Permissions seeded successfully!');
    }
}

