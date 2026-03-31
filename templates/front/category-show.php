<?php

$slug = $slug ?? '';
$category = category_find_by_slug((string) $slug);
$articles = article_by_category_slug((string) $slug, 24);

if (!$category) {
	http_response_code(404);
	$seo = seo_merge([
		'title' => 'Categorie introuvable | Iran News',
		'description' => 'La categorie demandee est introuvable.',
		'robots' => 'noindex,follow',
		'canonical' => url_for(current_path()),
	]);

	ob_start();
	?>
	<section class="hero">
		<h1>Categorie introuvable</h1>
		<p>La categorie que vous recherchez n'existe pas.</p>
	</section>
	<?php
	$content = ob_get_clean();
	require __DIR__ . '/../layouts/front.php';
	return;
}

$menuCategories = category_all_with_counts();
$seo = seo_merge([
	'title' => (string) $category['name'] . ' | Iran News',
	'description' => 'Articles de la categorie ' . (string) $category['name'] . ' sur le conflit en Iran.',
	'canonical' => url_for(category_url((string) $category['slug'])),
]);

ob_start();
?>
<section class="hero compact-hero">
	<p class="eyebrow">Categorie</p>
	<h1><?= e($category['name']) ?></h1>
	<p><?= count($articles) ?> article(s) publie(s)</p>
</section>

<?php if ($articles === []): ?>
	<p>Aucun article publie dans cette categorie.</p>
<?php else: ?>
	<section class="cards-grid" aria-label="Articles de la categorie <?= e($category['name']) ?>">
		<?php foreach ($articles as $article): ?>
			<?php require __DIR__ . '/partials/article-card.php'; ?>
		<?php endforeach; ?>
	</section>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/front.php';
