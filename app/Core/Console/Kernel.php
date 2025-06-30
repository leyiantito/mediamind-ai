<?php

namespace MediaMindAI\Core\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \MediaMindAI\Console\Commands\TestCommand::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        $this->load(app_path('Console/Commands'));

        require base_path('routes/console.php');
    }
}