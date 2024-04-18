<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InitialSetupLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:initial-setup {--with-env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all Dependencies, prepare database migration and seeder for local database, generate auth and finally compile Dependencies';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('with-env')) {
            exec('cp .env.example .env');
            $this->info('.env File successfully copied <3');

            exec('cp .env.testing.example .env.testing');
            $this->info('.env.testing File successfully copied <3');

            exec('cp phpunit.xml.example phpunit.xml');
            $this->info('phpunit.xml File successfully copied <3');

            exec('php artisan key:generate');
            $this->info('Application Key Successfully Added <3');
        }

        exec('composer install');
        exec('composer dumpautoload');
        $this->info('Composer Dependencies Successfully Added <3');

        exec('yarn install --frozen-lockfile');
        $this->info('Yarn Dependencies Successfully Added <3');

        try {
            $result = exec('php artisan migrate:fresh');
            $this->info($result);
            $this->info('Migrations Successfully Executed <3');
        } catch (\Throwable $th) {
            $this->error('there\'s something wrong with database. check if the database is exists or not');
            throw $th;
        }

        if (! File::exists('storage\oauth-private.key')) {
            $result = exec('php artisan passport:key');
            $this->info($result);
            $this->info('Passport Key Generated <3');
        }

        exec('php artisan db:seed --class=LocalSeeder');
        $this->info('Seeder Successfully Added <3');

        exec('yarn dev');
        $this->info('Dependencies Successfully Compiled <3');
    }
}
