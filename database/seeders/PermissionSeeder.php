<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            //permissions for Authorization module
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',

            //permission for course
            'course-by-id',
            'category-list',
            'course-list-pagination',

        ];
        foreach ($permissions as $name) {
            Permission::create([
                'name'        => $name
            ]);
        }
    }
}
