<?php

declare(strict_types=1);

return [
	'host' => getenv('DB_HOST') ?: 'db',
	'port' => getenv('DB_PORT') ?: '5432',
	'name' => getenv('DB_NAME') ?: 'iran_news',
	'user' => getenv('DB_USER') ?: 'postgres',
	'password' => getenv('DB_PASSWORD') ?: 'postgres',
];
