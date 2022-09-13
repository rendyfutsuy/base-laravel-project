<?php

namespace Database\Seeders;

use App\Models\TestExample;
use Illuminate\Database\Seeder;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $examples = [
            [
                'name' => 'Adit Saja',
                'status' => TestExample::ACTIVATED,
                'joined_at' => '2021-09-09 10:00:00',
            ],

            [
                'name' => 'Zanoba Shirone',
                'status' => TestExample::EXPIRED,
                'joined_at' => '2021-09-09 10:00:00',
            ],

            [
                'name' => 'luigi Gundam',
                'status' => TestExample::REJECTED,
                'joined_at' => '2021-09-09 10:00:00',
            ],

            [
                'name' => 'Rendy luigi ganteng',
                'status' => 1,
                'joined_at' => '2021-09-09 10:00:00',
            ],

            [
                'name' => 'luigi ganteng',
                'status' => TestExample::EXPIRED,
                'joined_at' => '2021-09-09 10:00:00',
            ],

            [
                'name' => 'Yusup',
                'status' => TestExample::REJECTED,
                'joined_at' => '2021-09-09 10:00:00',
            ],

            [
                'name' => 'Dianmund',
                'status' => TestExample::PENDING,
                'joined_at' => '2021-09-09 10:00:00',
            ],
        ];

        foreach ($examples as $example) {
            TestExample::create($example);
        }
    }
}
