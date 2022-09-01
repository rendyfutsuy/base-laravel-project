<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ReSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 're-setup {seeder : Class Name of Seeder file. example: LocalSeeder, LiveSeeder, StagingSeeder etc.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run composer install, yarn install, php artisan migrate:fresh, php artisan db:seed, yarn production';

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
        try {
            User::all();
        } catch (\Throwable $th) {
            $this->error('Database Not Found');
            return 0;
        }

        if (! $this->argument('seeder')) {
            $this->error('Please Insert Seeder Name <3');
            return 0;
        }

        $seeder = ucfirst($this->argument('seeder'));
        $filePath = 'database\seeders\\'. $seeder.'.php';
        
        if (! File::exists($filePath)) {
            $this->error('seeder file not found <3');
            return 0;
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
            return 0;
        }

        if (! File::exists('storage\oauth-private.key')) {
            $result = exec('php artisan passport:key');
            $this->info($result);
            $this->info('Passport Key Generated <3');
        }

        $result = exec('php artisan db:seed --class='. $seeder);
        $this->info($result);
        $this->info('Seeder Successfully Added <3');

        exec('yarn production');
        $this->info('Dependencies Successfully Compiled <3');
    }
}
