<?php
// Set a very high memory limit
ini_set('memory_limit', '2G');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Starting debug...\n";

// Define application paths
define('LARAVEL_START', microtime(true));
define('BASE_PATH', __DIR__);

// Load Composer's autoloader
echo "Loading Composer autoloader...\n";
require __DIR__.'/vendor/autoload.php';

// Create application instance with minimal configuration
echo "Creating application instance...\n";
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__)
);

// Set the application instance
Illuminate\Container\Container::setInstance($app);

// Minimal service providers
$app->register(\Illuminate\Events\EventServiceProvider::class);
$app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);

// Set up the basic bindings
$app->instance('app', $app);
$app->instance('path', __DIR__);
$app->instance('path.base', __DIR__);
$app->instance('path.config', __DIR__.'/config');
$app->instance('path.public', __DIR__.'/public');
$app->instance('path.storage', __DIR__.'/storage');
$app->instance('path.database', __DIR__.'/database');
$app->instance('path.resources', __DIR__.'/resources');
$app->instance('path.bootstrap', __DIR__.'/bootstrap');

// Bootstrap the application
echo "Bootstrapping application...\n";
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\SetRequestForConsole::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

echo "Application bootstrapped successfully!\n";