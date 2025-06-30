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
        //
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
        // Temporarily disabled loading commands to debug memory issue
        // $this->load(__DIR__.'/Commands');
        
        // Temporarily disabled console routes
        // $consoleRoutesPath = base_path('routes/console.php');
        // if (file_exists($consoleRoutesPath)) {
        //     require $consoleRoutesPath;
        // }
        
        // Load only the essential commands
        $this->loadLaravelCommands();
    }

    /**
     * Load Laravel's built-in commands.
     *
     * @return void
     */
    protected function loadLaravelCommands()
    {
        $this->app->singleton('command.cache.clear', function ($app) {
            return new \Illuminate\Cache\Console\ClearCommand($app['cache'], $app['files']);
        });
        $this->commands('command.cache.clear');

        $this->app->singleton('command.config.cache', function ($app) {
            return new \Illuminate\Foundation\Console\ConfigCacheCommand($app['files']);
        });
        $this->commands('command.config.cache');

        $this->app->singleton('command.key.generate', function () {
            return new \Illuminate\Foundation\Console\KeyGenerateCommand();
        });
        $this->commands('command.key.generate');
    }
}
