<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Sincronizar métricas de Google Search Console diariamente a las 2 AM
        $schedule->command('seo:sync-metrics --days=1')
            ->dailyAt('02:00')
            ->withoutOverlapping()
            ->runInBackground();

        // Detectar cambios de posición y tráfico diariamente
        // DESACTIVADO: Ahora es manual desde la interfaz (botón "Detectar Cambios")
        // $schedule->call(function () {
        //     $alertService = new \App\Services\AlertService();
        //     $alertService->detectPositionChanges();
        //     $alertService->detectTrafficDrops();
        // })->dailyAt('03:00');

        // Validar sitemap y robots.txt semanalmente
        $schedule->command('seo:validate-sitemap-robots')
            ->weeklyOn(1, '04:00'); // Lunes a las 4 AM
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
