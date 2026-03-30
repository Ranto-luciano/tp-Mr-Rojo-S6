<?php

declare(strict_types=1);

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = rtrim(getenv('APP_URL') ?: 'http://localhost:8080', '/');

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc><?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/</loc>
	</url>
	<url>
		<loc><?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>/search</loc>
	</url>
</urlset>
