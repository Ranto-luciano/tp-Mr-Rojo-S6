<?php

declare(strict_types=1);

function seo_defaults(): array
{
	static $defaults = null;
	if ($defaults !== null) {
		return $defaults;
	}

	$seo = require __DIR__ . '/../../config/seo.php';

	$defaults = [
		'title' => (string) $seo['default_title'],
		'description' => (string) $seo['default_description'],
		'robots' => (string) $seo['default_robots'],
		'canonical' => current_url(),
		'og_type' => 'website',
		'og_image' => (string) $seo['default_og_image'],
	];

	return $defaults;
}

function seo_merge(array $overrides = []): array
{
	return array_merge(seo_defaults(), $overrides);
}

function seo_tags(array $meta): string
{
	$meta = seo_merge($meta);
	$title = e($meta['title']);
	$description = e($meta['description']);
	$canonical = e((string) $meta['canonical']);
	$robots = e((string) $meta['robots']);
	$ogType = e((string) $meta['og_type']);

	$ogImage = (string) $meta['og_image'];
	if (!str_starts_with($ogImage, 'http')) {
		$ogImage = url_for($ogImage);
	}

	$ogImage = e($ogImage);

	return <<<HTML
<title>{$title}</title>
<meta name="description" content="{$description}">
<meta name="robots" content="{$robots}">
<link rel="canonical" href="{$canonical}">
<meta property="og:type" content="{$ogType}">
<meta property="og:title" content="{$title}">
<meta property="og:description" content="{$description}">
<meta property="og:url" content="{$canonical}">
<meta property="og:image" content="{$ogImage}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{$title}">
<meta name="twitter:description" content="{$description}">
<meta name="twitter:image" content="{$ogImage}">
HTML;
}
