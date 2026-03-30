<?php

declare(strict_types=1);

return [
	'name' => 'Iran News',
	'env' => getenv('APP_ENV') ?: 'development',
	'debug' => (getenv('APP_DEBUG') ?: 'true') === 'true',
	'url' => getenv('APP_URL') ?: 'http://localhost:8080',
	'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
];
