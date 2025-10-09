<?php
/**
 * Database initialization page for Vercel
 * Access this page once after deployment to initialize the database
 */

// Load environment variables
require_once __DIR__ . '/../config/bootstrap.php';

// Check if running on Vercel
if (!getenv('VERCEL')) {
    die('This script should only be run on Vercel');
}

// Database connection parameters
$host = getenv('MYSQL_HOST');
$port = getenv('MYSQL_PORT') ?: '3306';
$database = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

if (!$host || !$database || !$username) {
    die('Missing required database environment variables');
}

echo "<h1>FinAI Database Initialization</h1>";

try {
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✅ Database '$database' created successfully or already exists.</p>";
    
    // Connect to the specific database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Run migrations
    echo "<p>🔄 Running migrations...</p>";
    
    // Change to project root directory
    chdir(__DIR__ . '/..');
    
    // Run Yii migrations
    $command = "php yii migrate --interactive=0";
    $output = [];
    $returnCode = 0;
    
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "<p>✅ Migrations completed successfully!</p>";
        echo "<h2>Migration Output:</h2>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    } else {
        echo "<p>❌ Migration failed with return code: $returnCode</p>";
        echo "<h2>Error Output:</h2>";
        echo "<pre>" . implode("\n", $output) . "</pre>";
    }
    
    echo "<p><strong>Database initialization completed!</strong></p>";
    echo "<p><a href='/'>Go to main application</a></p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
