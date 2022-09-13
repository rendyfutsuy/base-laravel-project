<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OauthClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('oauth_clients')->updateOrInsert(
            [
                'id' => 1,
            ],
            [
                'name' => 'Personal Access Client',
                'secret' => 'oxXhAUwFLm9q7a6V4iDOe1IfSHoFwWwW5uwlLSt2',
                'provider' => null,
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('oauth_clients')->updateOrInsert(
            [
                'id' => '2',
            ],
            [
                'name' => 'Password Grant Client',
                'secret' => 'MurwvuSYQDNR0ujX4Nlwt1yXB8b0ieVM6cqlETJ6',
                'provider' => 'users',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('oauth_personal_access_clients')->updateOrInsert(
            [
                'id' => '1',
            ],
            [
                'client_id' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
