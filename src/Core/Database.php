<?php

declare(strict_types=1);

function db(): ?PDO
{
	static $pdo = null;
	static $initialized = false;

	if ($initialized) {
		return $pdo;
	}

	$initialized = true;
	$config = require __DIR__ . '/../../config/database.php';

	$dsn = sprintf(
		'pgsql:host=%s;port=%s;dbname=%s',
		$config['host'],
		$config['port'],
		$config['name']
	);

	try {
		$pdo = new PDO($dsn, (string) $config['user'], (string) $config['password'], [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		]);
	} catch (Throwable $exception) {
		$pdo = null;
	}

	return $pdo;
}
