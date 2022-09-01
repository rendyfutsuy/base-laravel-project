<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LocalTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'local:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run composer install, yarn install, php artisan migrate:fresh, php artisan db:seed --class=UnitTestingSeeder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        exec('composer install');
        $this->info('Composer Dependencies Successfully Added <3');

        try {
            $result = exec('php artisan migrate:fresh --env=testing');
            $this->info($result);
            $this->info('Migrations Successfully Executed <3');
        } catch (\Throwable $th) {
            $this->error('there\'s something wrong with database. check if the database is exists or not');
            throw $th;
            return 0;
        }

        if (! File::exists('storage\oauth-private.key')) {
            $result = exec('php artisan passport:key');
            $this->info($result);
            $this->info('Passport Key Generated <3');
        }

        exec('php artisan db:seed --env=testing --class=UnitTestingSeeder');
        $this->info('Seeder Successfully Added <3');

        try {
            $result = shell_exec('php artisan test --stop-on-failure');
            $this->info($result);
            $this->info('All Test Passed <3');
        } catch (\Throwable $th) {
            $this->error('Oh no, there\'s Failed Unit Test');
        }
    }
}
