<?php
/**
 * Database initialization script for Railway
 */

// Load environment variables
require_once __DIR__ . '/config/bootstrap.php';

// Database connection parameters
$host = getenv('MYSQL_HOST') ?: 'localhost';
$database = getenv('MYSQL_DATABASE') ?: 'finlatlv_app';
$username = getenv('MYSQL_USER') ?: 'root';
$password = getenv('MYSQL_PASSWORD') ?: '';

try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8 COLLATE utf8_general_ci");
    
    echo "Database '$database' created successfully or already exists.\n";
    
} catch (PDOException $e) {
    echo "Error creating database: " . $e->getMessage() . "\n";
    exit(1);
}

