<article class="article-card">
	<a class="article-card-image-link" href="<?= e(article_url((string) $article['slug'])) ?>">
		<img
			src="<?= e($article['image_path'] ?: '/assets/images/placeholders/og-default.jpg') ?>"
			alt="<?= e($article['image_alt'] ?: $article['title']) ?>"
			loading="lazy"
			width="640"
			height="360"
		>
	</a>
	<div class="article-card-body">
		<p class="eyebrow">
			<a href="<?= e(category_url((string) $article['category_slug'])) ?>"><?= e($article['category_name']) ?></a>
		</p>
		<h3>
			<a href="<?= e(article_url((string) $article['slug'])) ?>"><?= e($article['title']) ?></a>
		</h3>
		<p><?= e(excerpt_text((string) ($article['excerpt'] ?: $article['content']), 140)) ?></p>
		<p class="meta"><?= e(format_date($article['published_at'] ?? null)) ?></p>
	</div>
</article>
