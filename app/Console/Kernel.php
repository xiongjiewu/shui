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
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\CalculateTheBlackWater::class,
        \App\Console\Commands\RecyclingWaterValue::class,
        \App\Console\Commands\RecyclingShareWater::class,
        \App\Console\Commands\text::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
            ->hourly();

        $schedule->command('calculate_the_black_water')
            ->dailyAt('01:00');

        $schedule->command('recycling_water_value')
            ->cron('* */2 * * *');

        $schedule->command('recycling_share_water')
            ->cron('* */3 * * *');

        $schedule->command('text')
            ->cron('* * * * *');
    }
}
