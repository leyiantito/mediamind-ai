<?php
// Set a higher memory limit
ini_set('memory_limit', '1G');

// Manually set the application key in the .env file
$envFile = __DIR__.'/.env';
$key = 'base64:'.base64_encode(random_bytes(32));
$content = file_get_contents($envFile);
$content = preg_replace('/APP_KEY=.*/', "APP_KEY=$key", $content);
file_put_contents($envFile, $content);

echo "Application key set successfully: $key\n";