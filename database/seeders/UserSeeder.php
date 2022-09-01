<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'rendy@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);

        $role = Role::findByName('NORMAL_USER', 'api');
        $user->assignRole($role);

        $superadmin = User::create([
            'email' => 'superadmin@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);

        $role = Role::findByName('SUPER_ADMIN', 'api');
        $superadmin->assignRole($role);

        for ($i=0; $i < 20; $i++) { 
            $admin = User::create([
                'email' => 'admin.dummy.'.$i.'@mailinator.com',
                'password' => Hash::make('12345'),
                'name' => 'Rendy Anggara',
            ]);
    
            $role = Role::findByName('SUPER_ADMIN', 'api');
            $admin->assignRole($role);
        }

        $staff = User::create([
            'email' => 'staff@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);

        $role = Role::findByName('STAFF', 'api');
        $staff->assignRole($role);

        for ($i=0; $i < 20; $i++) { 
            $staff = User::create([
                'email' => 'staff.dummy.'.$i.'@mailinator.com',
                'password' => Hash::make('12345'),
                'name' => 'Rendy Anggara',
            ]);
    
            $role = Role::findByName('STAFF', 'api');
            $staff->assignRole($role);
        }

        $user = User::create([
            'email' => 'user@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);
        $role = Role::findByName('NORMAL_USER', 'api');
        $user->assignRole($role);

        $user = User::create([
            'email' => 'user.1@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);
        $role = Role::findByName('NORMAL_USER', 'api');
        $user->assignRole($role);

        for ($i=0; $i < 20; $i++) { 
            $user = User::create([
                'email' => 'user.dummy.'.$i.'@mailinator.com',
                'password' => Hash::make('12345'),
                'name' => 'Rendy Anggara',
            ]);
    
            $role = Role::findByName('NORMAL_USER', 'api');
            $user->assignRole($role);
        }

        $unvalidatedUser = User::create([
            'email' => 'unvalidatedUser.1@mailinator.com',
            'password' => Hash::make('12345'),
            'name' => 'Rendy Anggara',
        ]);

        $role = Role::findByName('UNVALIDATED_USER', 'api');        
        $unvalidatedUser->assignRole($role);
    }
}
