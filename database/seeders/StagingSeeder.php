<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RolesSeeder;
use Database\Seeders\ExampleSeeder;
use Database\Seeders\OauthClientSeeder;
use Database\Seeders\PermissionsSeeder;

class StagingSeeder extends Seeder
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
            OauthClientSeeder::class
        ]);
    }
}
