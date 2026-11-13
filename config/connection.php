<?php

$envFile = __DIR__ . '/../env/.env';

// Check environment file
if (!is_readable($envFile)) {
    die('Database configuration file not found or not readable.');
}

$config = parse_ini_file($envFile);

if ($config === false) {
    die('Unable to read database configuration.');
}

// Check required settings
$required = ['DB_HOST', 'DB_PORT', 'DB_NAME', 'DB_USER', 'DB_PASS'];

foreach ($required as $key) {
    if (!array_key_exists($key, $config)) {
        die("Missing configuration: {$key}");
    }
}

// Validate values
if (trim($config['DB_HOST']) === '' ||
    trim($config['DB_NAME']) === '' ||
    trim($config['DB_USER']) === '') {
    die('Database host, name and username cannot be empty.');
}

// connect to the database
try {
    $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";

    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());

    die('Database connection failed. Check your database settings and make sure MySQL is running.');
}
