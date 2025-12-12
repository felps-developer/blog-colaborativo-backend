<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // Descomente a linha acima se quiser agendar o comando inspire
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        // $this->load(__DIR__.'/Commands'); // Descomente se criar comandos personalizados

        require base_path('routes/console.php');
    }
}

