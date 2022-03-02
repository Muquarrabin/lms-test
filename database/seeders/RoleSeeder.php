<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $superAdmin = Role::create(
            ['name' =>'super-admin']
        );
        $admin = Role::create(
            ['name' =>'admin']
        );
        $student = Role::create(
            ['name' =>'student']
        );
        $instructor = Role::create(
            [
                'name' => 'instructor'
            ]
        );
        $superAdmin->givePermissionTo(Permission::all());
    }
}
