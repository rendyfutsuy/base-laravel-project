<?php

namespace App\Console;

use App\Console\Commands\ReSetup;
use App\Console\Commands\InitialSetup;
use App\Console\Commands\MakeHttpSearch;
use App\Console\Commands\MakeRepository;
use App\Console\Commands\EnvironmentSetup;
use App\Console\Commands\InitialSetupLocal;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\InitialSetupStaging;
use App\Console\Commands\MakeHttpSearchFilter;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        MakeHttpSearch::class,
        MakeHttpSearchFilter::class,
        MakeRepository::class,
        InitialSetup::class,
        InitialSetupLocal::class,
        InitialSetupStaging::class,
        ReSetup::class,
        EnvironmentSetup::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
