<?php

$slug = $slug ?? '';
$article = article_find_by_slug((string) $slug);

if (!$article) {
	http_response_code(404);
	$seo = seo_merge([
		'title' => 'Article introuvable | Iran News',
		'description' => 'L\'article demande est introuvable.',
		'robots' => 'noindex,follow',
		'canonical' => url_for(current_path()),
	]);

	ob_start();
	?>
	<section class="hero">
		<h1>Article introuvable</h1>
		<p>Le contenu demande n'existe pas ou n'est plus disponible.</p>
		<p><a class="text-link" href="/">Retour a l'accueil</a></p>
	</section>
	<?php
	$content = ob_get_clean();
	require __DIR__ . '/../layouts/front.php';
	return;
}

$menuCategories = category_all_with_counts();
$related = article_related((string) $article['category_slug'], (string) $article['slug'], 3);
$gallery = article_images_by_slug((string) $article['slug']);

$description = excerpt_text((string) ($article['excerpt'] ?: $article['content']), 155);
$seo = seo_merge([
	'title' => (string) $article['title'] . ' | Iran News',
	'description' => $description,
	'canonical' => url_for(article_url((string) $article['slug'])),
	'og_type' => 'article',
	'og_image' => (string) ($article['image_path'] ?: '/assets/images/placeholders/og-default.jpg'),
]);

ob_start();
?>
<article class="article-page">
	<header class="article-header">
		<p class="eyebrow">
			<a href="<?= e(category_url((string) $article['category_slug'])) ?>"><?= e($article['category_name']) ?></a>
		</p>
		<h1><?= e($article['title']) ?></h1>
		<p class="meta">
			<time datetime="<?= e((string) ($article['published_at'] ?: '')) ?>"><?= e(format_date($article['published_at'] ?? null)) ?></time>
			<span>·</span>
			<span><?= e((string) reading_time_minutes((string) $article['content'])) ?> min de lecture</span>
		</p>
	</header>

	<figure class="article-figure">
		<img
			src="<?= e($article['image_path'] ?: '/assets/images/placeholders/og-default.jpg') ?>"
			alt="<?= e($article['image_alt'] ?: $article['title']) ?>"
			width="1200"
			height="675"
		>
	</figure>

	<?php if ($gallery !== []): ?>
		<section class="article-gallery" aria-label="Galerie photo">
			<div class="section-head">
				<h2>Galerie photo</h2>
			</div>
			<div class="gallery-carousel" data-carousel>
				<button class="gallery-control prev" type="button" data-carousel-prev aria-label="Image precedente">‹</button>
				<div class="gallery-track" data-carousel-track>
					<?php foreach ($gallery as $image): ?>
						<figure class="gallery-item">
							<img
								src="<?= e($image['file_path'] ?: '/assets/images/placeholders/og-default.jpg') ?>"
								alt="<?= e($image['alt_text'] ?: $article['title']) ?>"
								loading="lazy"
							>
						</figure>
					<?php endforeach; ?>
				</div>
				<button class="gallery-control next" type="button" data-carousel-next aria-label="Image suivante">›</button>
			</div>
		</section>
	<?php endif; ?>

	<section class="article-body">
		<h2>Resume</h2>
		<p><?= nl2br(e((string) ($article['excerpt'] ?: excerpt_text((string) $article['content'], 260)))) ?></p>

		<h2>Contenu</h2>
		<p><?= nl2br(e((string) $article['content'])) ?></p>
	</section>
</article>

<?php if ($related !== []): ?>
	<section class="section-head">
		<h2>Articles lies</h2>
	</section>
	<section class="cards-grid" aria-label="Articles lies">
		<?php foreach ($related as $article): ?>
			<?php require __DIR__ . '/partials/article-card.php'; ?>
		<?php endforeach; ?>
	</section>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/front.php';
