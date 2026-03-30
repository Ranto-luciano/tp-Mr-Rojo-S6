<?php

$query = trim((string) ($_GET['q'] ?? ''));
$results = $query === '' ? [] : article_search($query, 30);
$menuCategories = category_all_with_counts();

$seo = seo_merge([
	'title' => $query === '' ? 'Recherche | Iran News' : 'Recherche: ' . $query . ' | Iran News',
	'description' => 'Recherche d\'articles sur le conflit en Iran.',
	'robots' => 'noindex,follow',
	'canonical' => $query === '' ? url_for('/search') : url_for('/search?q=' . rawurlencode($query)),
]);

ob_start();
?>
<section class="hero compact-hero">
	<p class="eyebrow">Recherche</p>
	<h1>Trouver un article</h1>
	<p>Recherche par mots-clefs dans le titre, l'extrait et le contenu.</p>
</section>

<form class="search-panel" action="/search" method="get">
	<label for="q">Mot-clef</label>
	<div class="search-panel-row">
		<input id="q" name="q" type="search" value="<?= e($query) ?>" required>
		<button type="submit">Rechercher</button>
	</div>
</form>

<?php if ($query !== ''): ?>
	<section class="section-head compact">
		<h2>Resultats pour "<?= e($query) ?>"</h2>
		<p><?= count($results) ?> resultat(s)</p>
	</section>

	<?php if ($results === []): ?>
		<p>Aucun article ne correspond a votre recherche.</p>
	<?php else: ?>
		<section class="cards-grid" aria-label="Resultats de recherche">
			<?php foreach ($results as $article): ?>
				<?php require __DIR__ . '/partials/article-card.php'; ?>
			<?php endforeach; ?>
		</section>
	<?php endif; ?>
<?php endif; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/front.php';
