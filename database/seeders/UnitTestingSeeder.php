<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UnitTestingSeeder extends Seeder
{
    /**
     * Seeder for Testing Environment.
     *
     * @return void
     */
    public function run()
    {
        // Run only Testing Environment
        // In Any Situation do not run this Seeder as Production or Staging

        $this->call([
            ExampleSeeder::class,
            RolesSeeder::class,
            PermissionsSeeder::class,
        ]);

        // If developer want to add another Seeder for Unit Testing purpose.
        // Add those here.

        $this->call([
            OauthClientSeeder::class,
            UserSeeder::class,
        ]);
    }
}
