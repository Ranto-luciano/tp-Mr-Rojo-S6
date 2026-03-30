<?php

$latestArticles = article_latest(9);
$menuCategories = category_all_with_counts();

$seo = seo_merge([
	'title' => 'Iran News | Actualites et analyses',
	'description' => 'Suivez des informations structurees sur le conflit en Iran : articles, analyses, chronologie et contexte.',
	'canonical' => url_for('/'),
	'og_type' => 'website',
]);

ob_start();
?>
<section class="hero">
	<p class="eyebrow">FrontOffice</p>
	<h1>Informations structurees sur la guerre en Iran</h1>
	<p>Des articles clairs, classes par categories, avec des contenus lisibles et optimises pour le SEO.</p>
</section>

<section class="section-head">
	<h2>Derniers articles</h2>
	<a href="/search" class="text-link">Voir la recherche</a>
</section>

<?php if ($latestArticles === []): ?>
	<p>Aucun article publie pour le moment.</p>
<?php else: ?>
	<section class="cards-grid" aria-label="Liste des derniers articles">
		<?php foreach ($latestArticles as $article): ?>
			<?php require __DIR__ . '/partials/article-card.php'; ?>
		<?php endforeach; ?>
	</section>
<?php endif; ?>

<section class="section-head compact">
	<h2>Categories</h2>
</section>
<section class="tag-list" aria-label="Liste des categories">
	<?php foreach ($menuCategories as $category): ?>
		<a href="<?= e(category_url((string) $category['slug'])) ?>" class="tag-item">
			<?= e($category['name']) ?>
			<span><?= e((string) $category['article_count']) ?></span>
		</a>
	<?php endforeach; ?>
</section>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/front.php';
