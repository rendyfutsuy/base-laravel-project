<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnvironmentSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:environment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'prepare environment file';

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
        exec('cp .env.example .env');
        $this->info('.env File successfully copied <3');

        exec('cp .env.testing.example .env.testing');
        $this->info('.env.testing File successfully copied <3');

        exec('cp phpunit.xml.example phpunit.xml');
        $this->info('phpunit.xml File successfully copied <3');

        exec('php artisan key:generate');
        $this->info('Application Key Successfully Added <3');
    }
}
