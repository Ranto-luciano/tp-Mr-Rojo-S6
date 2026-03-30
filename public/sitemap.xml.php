<?php

declare(strict_types=1);

require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Models/Category.php';
require __DIR__ . '/../src/Models/Article.php';
require __DIR__ . '/../src/Helpers/url.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = app_base_url();

$staticPaths = ['/', '/search'];
$categories = category_all_with_counts();
$articles = article_sitemap_items();

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($staticPaths as $path): ?>
		<url>
			<loc><?= htmlspecialchars($baseUrl . $path, ENT_QUOTES, 'UTF-8') ?></loc>
		</url>
	<?php endforeach; ?>

	<?php foreach ($categories as $category): ?>
		<url>
			<loc><?= htmlspecialchars($baseUrl . '/category/' . $category['slug'], ENT_QUOTES, 'UTF-8') ?></loc>
		</url>
	<?php endforeach; ?>

	<?php foreach ($articles as $article): ?>
		<url>
			<loc><?= htmlspecialchars($baseUrl . '/article/' . $article['slug'], ENT_QUOTES, 'UTF-8') ?></loc>
			<?php if (!empty($article['updated_at'])): ?>
				<lastmod><?= htmlspecialchars((new DateTimeImmutable((string) $article['updated_at']))->format('c'), ENT_QUOTES, 'UTF-8') ?></lastmod>
			<?php endif; ?>
		</url>
	<?php endforeach; ?>
</urlset>
