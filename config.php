<?php
// Check if Composer's autoload file exists and include it
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Try to load environment variables
    if (class_exists('Dotenv\Dotenv')) {
        // Load .env file if it exists
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->safeLoad(); // safeLoad won't throw an error if .env doesn't exist
    }
}

// Database configuration with fallbacks
$host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? '';
$dbname = $_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? 'finisterre_db';

// JWT Configuration
define('JWT_SECRET_KEY', $_ENV['JWT_SECRET_KEY'] ?? $_SERVER['JWT_SECRET_KEY'] ?? 'your_fallback_secret_key_here_change_this');
define('JWT_ISSUER', $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'http://localhost');
define('JWT_AUDIENCE', $_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'http://localhost');

// Create database connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>