#!/usr/bin/env php
<?php
// Set high memory limit
ini_set('memory_limit', '2G');

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Get the console kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Run the command
$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

$kernel->terminate($input, $status);

exit($status);