<?php

$latestArticles = article_latest(9);
$menuCategories = category_all_with_counts();
$featured = $latestArticles[0] ?? null;
$secondary = array_slice($latestArticles, 1, 2);
$stream = array_slice($latestArticles, 3);

$seo = seo_merge([
	'title' => 'Iran News | Fil info Iran, analyses et decryptage',
	'description' => 'Edition continue sur la crise en Iran: decisions politiques, impacts economiques et situation humanitaire.',
	'canonical' => url_for('/'),
	'og_type' => 'website',
]);

ob_start();
?>
<section class="hero hero-newsroom">
	<p class="eyebrow">Edition en continu</p>
	<h1>Iran News</h1>
	<p>Couverture structuree de l'actualite iranienne avec approche factuelle, chronologique et thematique.</p>
</section>

<?php if ($featured): ?>
	<section class="lead-grid" aria-label="Article principal">
		<article class="lead-story">
			<a href="<?= e(article_url((string) $featured['slug'])) ?>" class="lead-media">
				<img
					src="<?= e($featured['image_path'] ?: '/assets/images/placeholders/og-default.jpg') ?>"
					alt="<?= e($featured['image_alt'] ?: $featured['title']) ?>"
					width="1200"
					height="675"
				>
			</a>
			<div class="lead-content">
				<p class="eyebrow"><?= e($featured['category_name']) ?></p>
				<h2><a href="<?= e(article_url((string) $featured['slug'])) ?>"><?= e($featured['title']) ?></a></h2>
				<p><?= e(excerpt_text((string) ($featured['excerpt'] ?: $featured['content']), 170)) ?></p>
				<p class="meta"><?= e(format_date($featured['published_at'] ?? null)) ?></p>
			</div>
		</article>

		<aside class="side-highlights" aria-label="Titres a la une">
			<h2>A la une</h2>
			<ul>
				<?php foreach ($secondary as $item): ?>
					<li>
						<a href="<?= e(article_url((string) $item['slug'])) ?>"><?= e($item['title']) ?></a>
						<small><?= e(format_date($item['published_at'] ?? null)) ?></small>
					</li>
				<?php endforeach; ?>
			</ul>
		</aside>
	</section>
<?php endif; ?>

<section class="section-head">
	<h2>Fil info</h2>
	<a href="<?= e(search_url()) ?>" class="text-link">Recherche avancee</a>
</section>

<?php if ($stream === [] && $featured === null): ?>
	<p>Aucun article publie pour le moment.</p>
<?php else: ?>
	<section class="cards-grid" aria-label="Fil d'actualite">
		<?php foreach ($stream as $article): ?>
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
