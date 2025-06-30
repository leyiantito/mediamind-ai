<?php
// Set a high memory limit
ini_set('memory_limit', '2G');
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "Testing application key...\n";

// Load Composer's autoloader
require __DIR__.'/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get the key from .env
$key = env('APP_KEY');
if (empty($key)) {
    die("❌ Error: APP_KEY is not set in .env file\n");
}

// Remove 'base64:' prefix if present
$key = str_replace('base64:', '', $key);

// Determine the cipher based on key length
$cipher = 'aes-128-cbc'; // Default to 16-byte key
if (strlen(base64_decode($key)) === 32) {
    $cipher = 'aes-256-cbc'; // Use 32-byte key if available
}

echo "Using cipher: $cipher\n";
echo "Key length: " . strlen(base64_decode($key)) . " bytes\n";

try {
    $encrypter = new \Illuminate\Encryption\Encrypter(
        base64_decode($key),
        $cipher
    );
    
    $testData = 'Test encryption';
    echo "Original data: $testData\n";
    
    $encrypted = $encrypter->encrypt($testData);
    echo "Encrypted: $encrypted\n";
    
    $decrypted = $encrypter->decrypt($encrypted);
    echo "Decrypted: $decrypted\n";
    
    if ($testData === $decrypted) {
        echo "✅ Key is working correctly!\n";
    } else {
        echo "❌ Key test failed: Decrypted data doesn't match\n";
    }
} catch (Exception $e) {
    echo "❌ Error testing key: " . $e->getMessage() . "\n";
}