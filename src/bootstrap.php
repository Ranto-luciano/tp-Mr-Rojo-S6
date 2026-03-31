<?php

declare(strict_types=1);

require __DIR__ . '/Core/Database.php';
require __DIR__ . '/Helpers/url.php';
require __DIR__ . '/Helpers/format.php';
require __DIR__ . '/Helpers/seo.php';
require __DIR__ . '/Models/Category.php';
require __DIR__ . '/Models/Article.php';
require __DIR__ . '/Helpers/content.php';

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

// Ensure uploaded files stored in /storage are reachable from the web via /storage.
$projectRoot = dirname(__DIR__);
$storageDir = $projectRoot . '/storage';
$publicStorageLink = $projectRoot . '/public/storage';

if (is_dir($storageDir) && !is_dir($publicStorageLink) && !is_link($publicStorageLink)) {
	@symlink($storageDir, $publicStorageLink);
}
