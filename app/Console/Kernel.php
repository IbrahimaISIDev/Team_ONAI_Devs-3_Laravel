<?php

namespace App\Console;

use App\Services\MessageService;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\DispatchArchiveDettesPayees;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DispatchRecapitulatifHebdomadaire;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('dispatch:retry-failed-uploads')->daily();
        $schedule->command('dispatch:archive-dettes-payees')->daily();
        //$schedule->command('dispatch:archivedettespayees')->daily();
        $schedule->command('dispatch:recap-hebdo')->weekly();

        // Envoie le récapitulatif des dettes chaque lundi à 8h
        $schedule->call(function () {
            app(MessageService::class)->envoyerRecapitulatifHebdomadaire();
        })->weeklyOn(1, '8:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        \App\Console\Commands\ServeCommand::class,
        \App\Console\Commands\RetryUploadsCommand::class,
        \App\Console\Commands\DispatchArchiveDettesPayees::class,
        \App\Console\Commands\DispatchRecapitulatifHebdomadaire::class,
    ];
}
