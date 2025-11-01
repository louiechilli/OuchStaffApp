<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates all roles used throughout the application
     * based on role usage found in the codebase.
     */
    public function run(): void
    {
        $roles = [
            'inventory',
            'artist',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role],
                ['guard_name' => 'web']
            );
        }

        $this->command->info('Roles seeded successfully!');
    }
}

