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
    protected $signature = 'local:test {--with-code-fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run composer install,
    yarn install, php artisan migrate:fresh,
    php artisan db:seed --class=UnitTestingSeeder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        exec('composer install');
        $this->info('Composer Dependencies Successfully Added <3');

        exec('php artisan optimize:clear');
        $this->info('Clear previous config and load new config.. <3');

        $filepath = __DIR__.'/../../../vendor/bin/pint';

        $code = null;
        $outputs = null;
        if ($this->option('with-code-fix')) {
            exec($filepath.' -v', $outputs, $code);
        } else {
            exec($filepath.' --test', $outputs, $code);
        }

        if ($code == 1) {
            foreach ($outputs as $output) {
                $this->error($output);
            }
            $this->error('Pint Testing have Errors');
        } else {
            foreach ($outputs as $output) {
                $this->info($output);
            }
            $this->info('Pint Testing Initiated <3');
        }

        try {
            $result = shell_exec('php artisan migrate:fresh --env=testing');
            $this->info($result);
            $this->info('Migrations Successfully Executed <3');
        } catch (\Throwable $th) {
            $this->error('there\'s something wrong with database. check if the database is exists or not');
            throw $th;

            return 0;
        }

        if (! File::exists('storage\oauth-private.key')) {
            // Since Using Passport UUID Type,
            // we have to use command below to generate passport key
            $result = exec('php artisan passport:install --uuids --force -n');
            $this->info($result);
            $this->info('Passport Key Generated <3');
        }

        $result = shell_exec('php artisan db:seed --env=testing --class=UnitTestingSeeder');
        $this->info($result);
        $this->info('Seeder Successfully Added <3');

        $result = shell_exec('php artisan optimize:clear');
        $this->info($result);
        $this->info('Environment has been Optimize <3');

        try {
            $result = shell_exec('php artisan test --stop-on-failure --stop-on-risky --log-junit storage/logs/testing/tests.xml');
            $this->info($result);
            $this->info('All Test Passed <3');
        } catch (\Throwable $th) {
            $this->error('Oh no, there\'s Failed Unit Test');
        }
    }
}
