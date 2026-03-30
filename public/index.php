<?php

declare(strict_types=1);

require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Helpers/url.php';
require __DIR__ . '/../src/Helpers/format.php';
require __DIR__ . '/../src/Helpers/seo.php';
require __DIR__ . '/../src/Models/Category.php';
require __DIR__ . '/../src/Models/Article.php';

$appConfig = require __DIR__ . '/../config/app.php';
date_default_timezone_set($appConfig['timezone']);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($path === '/sitemap.xml') {
	require __DIR__ . '/sitemap.xml.php';
	exit;
}

if ($path === '/admin/login') {
	require __DIR__ . '/../templates/back/auth/login.php';
	exit;
}

if ($path === '/' || $path === '') {
	require __DIR__ . '/../templates/front/home.php';
	exit;
}

if ($path === '/search') {
	require __DIR__ . '/../templates/front/search.php';
	exit;
}

if (preg_match('#^/article/([a-z0-9-]+)$#i', $path, $matches) === 1) {
	$slug = $matches[1];
	require __DIR__ . '/../templates/front/article-show.php';
	exit;
}

if (preg_match('#^/category/([a-z0-9-]+)$#i', $path, $matches) === 1) {
	$slug = $matches[1];
	require __DIR__ . '/../templates/front/category-show.php';
	exit;
}

http_response_code(404);
$seo = seo_merge([
	'title' => '404 | Page introuvable',
	'description' => 'La page demandee est introuvable.',
	'robots' => 'noindex,follow',
	'canonical' => url_for($path),
]);

ob_start();
?>
<section class="hero">
	<h1>404 - Page introuvable</h1>
	<p>Le contenu demande n'existe pas.</p>
	<p><a class="text-link" href="/">Retour a l'accueil</a></p>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../templates/layouts/front.php';
