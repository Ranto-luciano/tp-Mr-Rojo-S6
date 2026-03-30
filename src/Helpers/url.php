<?php

declare(strict_types=1);

function app_base_url(): string
{
	static $base = null;
	if ($base !== null) {
		return $base;
	}

	$app = require __DIR__ . '/../../config/app.php';
	$base = rtrim((string) $app['url'], '/');

	return $base;
}

function url_for(string $path = '/'): string
{
	$path = '/' . ltrim($path, '/');
	return app_base_url() . $path;
}

function article_url(string $slug): string
{
	return '/actualites/article-' . rawurlencode($slug) . '.html';
}

function category_url(string $slug): string
{
	return '/rubriques/' . rawurlencode($slug) . '.html';
}

function search_url(): string
{
	return '/recherche.html';
}

function current_path(): string
{
	return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
}

function current_url(): string
{
	$uri = $_SERVER['REQUEST_URI'] ?? '/';
	return app_base_url() . $uri;
}
