<?php
// Increase memory limit
ini_set('memory_limit', '1G');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define application paths
define('LARAVEL_START', microtime(true));
define('BASE_PATH', __DIR__);

// Load Composer's autoloader
require __DIR__.'/vendor/autoload.php';

// Create application instance
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__)
);

// Bind important interfaces
$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

// Set the application instance
Illuminate\Container\Container::setInstance($app);

// Bootstrap the application
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

// Run the command
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);