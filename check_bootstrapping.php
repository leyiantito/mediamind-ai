<?php
// Set a very high memory limit
ini_set('memory_limit', '2G');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Starting bootstrap check...\n";

// Define application paths
define('LARAVEL_START', microtime(true));
define('BASE_PATH', __DIR__);

// Load Composer's autoloader
echo "1. Loading Composer autoloader...\n";
require __DIR__.'/vendor/autoload.php';

// Create application instance
echo "2. Creating application instance...\n";
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__)
);

// Set the application instance
Illuminate\Container\Container::setInstance($app);

// Set up basic bindings
echo "3. Setting up basic bindings...\n";
$app->instance('app', $app);
$app->instance('path', __DIR__);
$app->instance('path.base', __DIR__);

// Try to load environment
echo "4. Loading environment...\n";
try {
    (new \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables())->bootstrap($app);
    echo "✅ Environment loaded successfully\n";
} catch (Exception $e) {
    echo "⚠️ Error loading environment: " . $e->getMessage() . "\n";
}

// Try to load configuration
echo "5. Loading configuration...\n";
try {
    (new \Illuminate\Foundation\Bootstrap\LoadConfiguration())->bootstrap($app);
    echo "✅ Configuration loaded successfully\n";
    
    // List loaded configuration files
    $configPath = $app->configPath();
    echo "   Found configuration files:\n";
    foreach (glob("$configPath/*.php") as $file) {
        echo "   - " . basename($file) . "\n";
    }
} catch (Exception $e) {
    echo "⚠️ Error loading configuration: " . $e->getMessage() . "\n";
}

echo "Bootstrap check completed.\n";