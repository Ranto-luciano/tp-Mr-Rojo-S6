<?php
$menuCategories = $menuCategories ?? category_all_with_counts();
$path = current_path();
?>
<header class="site-header">
	<div class="container header-top">
		<a class="brand" href="/" aria-label="Retour a l'accueil Iran News">
			<span class="brand-mark" aria-hidden="true"></span>
			<span>Iran News</span>
		</a>
		<form class="search-form" action="<?= e(search_url()) ?>" method="get" role="search">
			<label for="search-input" class="sr-only">Rechercher un article</label>
			<input id="search-input" name="q" type="search" placeholder="Rechercher..." value="<?= e($_GET['q'] ?? '') ?>">
			<button type="submit">Chercher</button>
		</form>
	</div>

	<nav class="container nav" aria-label="Navigation principale">
		<a class="<?= $path === '/' ? 'active' : '' ?>" href="/">Accueil</a>
		<?php foreach (array_slice($menuCategories, 0, 5) as $category): ?>
			<a class="<?= $path === category_url((string) $category['slug']) ? 'active' : '' ?>"
			   href="<?= e(category_url((string) $category['slug'])) ?>">
				<?= e($category['name']) ?>
			</a>
		<?php endforeach; ?>
	</nav>
</header>
