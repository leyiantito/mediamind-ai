<?php

require_once __DIR__.'/../vendor/autoload.php';

use MediaMindAI\Core\Application;

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Initialize the application
$app = new Application(dirname(__DIR__));

// Register service providers
// $app->register(\MediaMindAI\Providers\AppServiceProvider::class);

// Load routes
require_once __DIR__.'/../routes/web.php';

// Run the application
$app->run();
