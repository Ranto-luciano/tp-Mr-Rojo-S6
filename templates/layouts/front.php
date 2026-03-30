<!doctype html>
<html lang="fr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?= seo_tags($seo ?? []) ?>
	<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
	<?php require __DIR__ . '/../front/partials/header.php'; ?>
	<main class="container main-content" id="main-content">
		<?= $content ?? '' ?>
	</main>
	<?php require __DIR__ . '/../front/partials/footer.php'; ?>
	<script src="/assets/js/main.js" defer></script>
</body>
</html>
