<?php
/**
 * Database initialization for Vercel
 * This script should be run once after deployment
 */

// Load environment variables
require_once __DIR__ . '/config/bootstrap.php';

// Database connection parameters for Vercel
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT') ?: '3306';
$database = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

if (!$host || !$database || !$username) {
    echo "Missing required database environment variables\n";
    exit(1);
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    echo "Database '$database' initialized successfully\n";
    
    // Run migrations
    $command = "php yii migrate --interactive=0";
    $output = [];
    $returnCode = 0;
    
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "Migrations completed successfully\n";
    } else {
        echo "Migration failed with return code: $returnCode\n";
        echo "Output: " . implode("\n", $output) . "\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
    exit(1);
}
