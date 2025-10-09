<?php
/**
 * Vercel database configuration
 * This file will be used when deploying to Vercel
 */

// Vercel provides these environment variables automatically
$vercelDbConfig = [
    'class' => 'yii\\db\\Connection',
    'dsn' => 'mysql:host=' . (getenv('MYSQL_HOST') ?: 'localhost') . 
             ';port=' . (getenv('MYSQL_PORT') ?: '3306') . 
             ';dbname=' . (getenv('MYSQL_DATABASE') ?: 'finlatlv_app'),
    'username' => getenv('MYSQL_USER') ?: 'root',
    'password' => getenv('MYSQL_PASSWORD') ?: '',
    'charset' => 'utf8',
    'attributes' => [
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ],
];

return $vercelDbConfig;
