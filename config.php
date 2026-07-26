<?php

/**
 * Load environment variables from .env file
 */
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value pairs
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if (($value[0] == '"' && $value[strlen($value) - 1] == '"') ||
                ($value[0] == "'" && $value[strlen($value) - 1] == "'")) {
                $value = substr($value, 1, -1);
            }
            
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    return true;
}

// Load .env from project root
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    loadEnv($envPath);
} else {
    // Try current directory
    loadEnv('.env');
}

/**
 * Get environment variable with default value
 */
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    return $value;
}

// Firebase Configuration
define('FIREBASE_API_KEY', env('FIREBASE_API_KEY', ''));
define('FIREBASE_AUTH_DOMAIN', env('FIREBASE_AUTH_DOMAIN', ''));
define('FIREBASE_PROJECT_ID', env('FIREBASE_PROJECT_ID', ''));
define('FIREBASE_STORAGE_BUCKET', env('FIREBASE_STORAGE_BUCKET', ''));
define('FIREBASE_MESSAGING_SENDER_ID', env('FIREBASE_MESSAGING_SENDER_ID', ''));
define('FIREBASE_APP_ID', env('FIREBASE_APP_ID', ''));
define('FIREBASE_MEASUREMENT_ID', env('FIREBASE_MEASUREMENT_ID', ''));

// Base URL
define('BASE_URL', env('BASE_URL', ''));

// Admin Credentials
define('ADMIN_EMAIL', env('ADMIN_EMAIL', 'admin@grin.com'));
define('ADMIN_PASSWORD', env('ADMIN_PASSWORD', 'admin123'));

// App Name
define('APP_NAME', env('APP_NAME', 'Grin Dental Clinic'));
