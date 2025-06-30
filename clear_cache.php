<?php
// Set a very high memory limit
ini_set('memory_limit', '2G');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Starting cache clearing process...\n";

// Define paths
$basePath = __DIR__;
$cachePath = $basePath . '/bootstrap/cache';
$storagePath = $basePath . '/storage/framework/views';
$configPath = $basePath . '/bootstrap/cache/config.php';

// Clear configuration cache
if (file_exists($configPath)) {
    unlink($configPath);
    echo "✅ Removed config cache\n";
}

// Clear compiled class files
$files = glob($cachePath . '/*.php');
foreach ($files as $file) {
    if (is_file($file)) {
        unlink($file);
        echo "✅ Removed: " . basename($file) . "\n";
    }
}

// Clear view cache
if (is_dir($storagePath)) {
    $views = glob($storagePath . '/*');
    foreach ($views as $view) {
        if (is_file($view)) {
            unlink($view);
        } elseif (is_dir($view)) {
            array_map('unlink', glob($view . '/*'));
            rmdir($view);
        }
    }
    echo "✅ Cleared view cache\n";
}

echo "✅ Cache cleared successfully!\n";