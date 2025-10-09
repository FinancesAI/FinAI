<?php

// Check if running on Vercel
if (getenv('VERCEL')) {
    // Vercel configuration
    return [
        'class' => 'yii\db\Connection',
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
} else {
    // Local/other hosting configuration
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=' . (getenv('DB_HOST') ?: 'localhost') . ';dbname=' . (getenv('DB_NAME') ?: 'finlatlv_app'),
        'username' => getenv('DB_USER') ?: 'finlatlv_app',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8',
    ];
}



