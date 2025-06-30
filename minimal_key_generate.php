<?php
// Set a very high memory limit
ini_set('memory_limit', '2G');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Starting minimal key generation...\n";

// Load only the essential files
require __DIR__.'/vendor/autoload.php';

// Create a minimal application instance
$app = new \Illuminate\Container\Container;
$app->instance('app', $app);
$app->instance('path', __DIR__);
$app->instance('path.base', __DIR__);

// Generate a new key
$key = 'base64:' . base64_encode(random_bytes(32));

// Update the .env file
$envFile = __DIR__.'/.env';
if (file_exists($envFile)) {
    $content = file_get_contents($envFile);
    $content = preg_replace('/^APP_KEY=.*/m', 'APP_KEY='.$key, $content);
    file_put_contents($envFile, $content);
    echo "Key generated and updated successfully!\n";
    echo "New APP_KEY: $key\n";
} else {
    echo "Error: .env file not found!\n";
    echo "Generated key (not saved): $key\n";
}