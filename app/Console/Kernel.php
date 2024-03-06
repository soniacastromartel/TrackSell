<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'app\Console\Commands\A3EmpleadosCron',
        'app\Console\Commands\A3Download',
        'app\Console\Commands\UpdateOldPendingServicesCron',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('a3empleados:cron')->everyMinute();
        $schedule->command('a3:download');
        $schedule->command('a3empleados:cron');
        //TODO - DEFINE HOW TO INVOCATE THIS COMMAND
        $schedule->command('services:update_old_pending_services')->weekly();
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
