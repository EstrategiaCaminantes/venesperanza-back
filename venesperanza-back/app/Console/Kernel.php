<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\FormulariosKobo;
use App\Console\Commands\NotificacionWhatsapp;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\FormulariosKobo::Class,
        Commands\NotificacionWhatsapp::Class
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
        //$schedule->command('formularioskobo:task')->everyMinute(); //pruebalocal
        $schedule->command('formularioskobo:task')->hourly();
        //$schedule->command('notificacionwhatsapp:task')->everyMinute(); //pruebalocal
        $schedule->command('notificacionwhatsapp:task')->twiceDaily(13, 23);

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
