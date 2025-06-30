<?php
// Set a very high memory limit
ini_set('memory_limit', '2G');

// Path to your .env file
$envFile = __DIR__ . '/.env';

// Generate a new key
$key = 'base64:' . base64_encode(random_bytes(32));

// Read the current .env content
$content = file_get_contents($envFile);

// Replace the APP_KEY line
$newContent = preg_replace(
    '/^APP_KEY=.*/m',
    'APP_KEY=' . $key,
    $content
);

// Write the new content back to .env
file_put_contents($envFile, $newContent);

echo "New application key has been set!\n";
echo "New Key: " . $key . "\n";