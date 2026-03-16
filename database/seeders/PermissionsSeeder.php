<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Permission Index', 'group_name' => 'Access Management Permissions'],
            ['name' => 'Permission Create', 'group_name' => 'Access Management Permissions'],
            ['name' => 'Permission Update', 'group_name' => 'Access Management Permissions'],
            ['name' => 'Permission Delete', 'group_name' => 'Access Management Permissions'],

            ['name' => 'Role Index', 'group_name' => 'Access Management Permissions'],
            ['name' => 'Role Create', 'group_name' => 'Access Management Permissions'],
            ['name' => 'Role Update', 'group_name' => 'Access Management Permissions'],
            ['name' => 'Role Delete', 'group_name' => 'Access Management Permissions'],

            ['name' => 'User Index', 'group_name' => 'User Management Permissions'],
            ['name' => 'User Create', 'group_name' => 'User Management Permissions'],
            ['name' => 'User Update', 'group_name' => 'User Management Permissions'],
            ['name' => 'User Delete', 'group_name' => 'User Management Permissions'],
            ['name' => 'User Toggle Status', 'group_name' => 'User Management Permissions'],

            ['name' => 'Petty Cash Index', 'group_name' => 'Petty Cash Management Permissions'],
            ['name' => 'Petty Cash Create', 'group_name' => 'Petty Cash Management Permissions'],
            ['name' => 'Petty Cash Delete', 'group_name' => 'Petty Cash Management Permissions'],
            ['name' => 'Petty Cash Verify', 'group_name' => 'Petty Cash Management Permissions'],
            ['name' => 'Petty Cash Approve', 'group_name' => 'Petty Cash Management Permissions'],
            ['name' => 'Petty Cash Update Payment Status', 'group_name' => 'Petty Cash Management Permissions'],

            ['name' => 'Category Index', 'group_name' => 'Category Management Permissions'],
            ['name' => 'Category Store', 'group_name' => 'Category Management Permissions'],
            ['name' => 'Category Show', 'group_name' => 'Category Management Permissions'],
            ['name' => 'Category Update', 'group_name' => 'Category Management Permissions'],
            ['name' => 'Category Destroy', 'group_name' => 'Category Management Permissions'],

            ['name' => 'Branch Index', 'group_name' => 'Branch Management Permissions'],
            ['name' => 'Branch Create', 'group_name' => 'Branch Management Permissions'],
            ['name' => 'Branch Show', 'group_name' => 'Branch Management Permissions'],
            ['name' => 'Branch Update', 'group_name' => 'Branch Management Permissions'],
            ['name' => 'Branch Delete', 'group_name' => 'Branch Management Permissions'],
            ['name' => 'Branch Toggle Status', 'group_name' => 'Branch Management Permissions'],

            ['name' => 'Department Index', 'group_name' => 'Department Management Permissions'],
            ['name' => 'Department Create', 'group_name' => 'Department Management Permissions'],
            ['name' => 'Department Show', 'group_name' => 'Department Management Permissions'],
            ['name' => 'Department Update', 'group_name' => 'Department Management Permissions'],
            ['name' => 'Department Delete', 'group_name' => 'Department Management Permissions'],
            ['name' => 'Department Toggle Status', 'group_name' => 'Department Management Permissions'],

            ['name' => 'Notification Index', 'group_name' => 'Notification Management Permissions'],
            ['name' => 'Notification Mark Read', 'group_name' => 'Notification Management Permissions'],
            ['name' => 'Notification Mark All Read', 'group_name' => 'Notification Management Permissions'],

            ['name' => 'Activity Log Index', 'group_name' => 'Log Management Permissions'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'group_name' => $permission['group_name'],
                'guard_name' => 'api',
            ]);
        }

        $role = Role::firstOrCreate(['guard_name' => 'api', 'name' => 'Super Admin']);

        $allPermissions = Permission::all();
        $role->syncPermissions($allPermissions);
    }
}
