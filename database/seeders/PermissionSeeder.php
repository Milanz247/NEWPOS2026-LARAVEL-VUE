<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage inventory',
            'make sales',
            'buyback phones',
            'view reports',
            'manage users',
            'manage roles',
            'manage business settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $ownerRole->syncPermissions(Permission::all());
    }
}
