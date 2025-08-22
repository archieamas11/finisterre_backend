<?php
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed_origins = [
    'https://finisterre.vercel.app',
    'https://finisterre-git-new-router-archiealbarico69-gmailcoms-projects.vercel.app',
    'https://finisterre-git-valha-9a5467-archiealbarico69-gmailcoms-projects.vercel.app'
];
if (in_array($origin, $allowed_origins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
} else {
    // For development: allow localhost with any port
    if (preg_match('/^http:\/\/localhost:\d+$/', $origin)) {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    }
}
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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