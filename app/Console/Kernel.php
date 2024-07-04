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
    //TODO Uso de ::class permite que PHP resuelva automáticamente el nombre completo de la clase, incluyendo el namespace. Esto reduce errores tipográficos y hace que el código sea más fácil de refactorizar.
    protected $commands = [
        \App\Console\Commands\A3EmpleadosCron::class,
        \App\Console\Commands\A3Download::class,
        \App\Console\Commands\UpdateOldPendingServicesCron::class,
    ];
    
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
         //TODO - DEFINE HOW TO INVOCATE THIS COMMAND
        $schedule->command('a3:download');
        $schedule->command('a3empleados:cron');
        $schedule->command('services:update_old_pending_services')->daily();
     
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
