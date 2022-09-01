<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'SUPER_ADMIN', 'guard_name' => 'api']);
        Role::create(['name' => 'STAFF', 'guard_name' => 'api']);
        Role::create(['name' => 'NORMAL_USER', 'guard_name' => 'api']);
        Role::create(['name' => 'UNVALIDATED_USER', 'guard_name' => 'api']);
    }
}
