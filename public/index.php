<?php

declare(strict_types=1);

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($path === '/' || $path === '') {
	require __DIR__ . '/../templates/front/home.php';
	exit;
}

if ($path === '/admin/login') {
	require __DIR__ . '/../templates/back/auth/login.php';
	exit;
}

http_response_code(404);
echo '404 - Page not found';
