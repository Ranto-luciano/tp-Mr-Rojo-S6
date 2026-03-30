<?php
$host = getenv('DB_HOST') ?: 'db';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'iran_news';
$user = getenv('DB_USER') ?: 'postgres';
$password = getenv('DB_PASSWORD') ?: 'postgres';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
    PDO::ATTR_EMULATE_PREPARES => false, 
];

try {
    $pdo = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données. Contactez l\'administrateur.');
}

return $pdo;