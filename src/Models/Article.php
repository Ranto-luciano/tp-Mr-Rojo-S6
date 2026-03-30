<?php

declare(strict_types=1);

function article_fallback_data(): array
{
	return [
		[
			'id' => 1,
			'title' => 'Iran renforce son dispositif interieur face au risque de contestation',
			'slug' => 'iran-renforce-dispositif-interieur-risque-contestation',
			'excerpt' => 'Les autorites multiplient les controles et les interpellations dans plusieurs villes.',
			'content' => 'Synthese redactionnelle: les mesures de securite interieure se renforcent dans un contexte de forte tension.',
			'published_at' => '2026-03-30 09:10:00',
			'updated_at' => '2026-03-30 11:00:00',
			'category_name' => 'Politique',
			'category_slug' => 'politique',
			'image_path' => '/assets/images/placeholders/og-default.jpg',
			'image_alt' => 'Controle de securite en zone urbaine',
		],
		[
			'id' => 2,
			'title' => 'Washington et Teheran: les propositions de paix restent contestees',
			'slug' => 'washington-teheran-propositions-paix-contestees',
			'excerpt' => 'Les discussions se poursuivent, mais les positions officielles divergent.',
			'content' => 'Point diplomatique: les canaux de dialogue restent ouverts, sans compromis public sur les points les plus sensibles.',
			'published_at' => '2026-03-30 12:20:00',
			'updated_at' => '2026-03-30 12:40:00',
			'category_name' => 'Diplomatie',
			'category_slug' => 'diplomatie',
			'image_path' => '/assets/images/placeholders/og-default.jpg',
			'image_alt' => 'Rencontre diplomatique sur la crise regionale',
		],
		[
			'id' => 3,
			'title' => 'Le conflit regional pese sur les perspectives economiques internationales',
			'slug' => 'conflit-regional-pese-perspectives-economiques-internationales',
			'excerpt' => 'Les institutions economiques alertent sur les effets de contagion.',
			'content' => 'Analyse economique: inflation importee, commerce perturbe et incertitude durable pour les pays exposes.',
			'published_at' => '2026-03-30 13:05:00',
			'updated_at' => '2026-03-30 13:20:00',
			'category_name' => 'Economie',
			'category_slug' => 'economie',
			'image_path' => '/assets/images/placeholders/og-default.jpg',
			'image_alt' => 'Courbe economique et indicateurs de risque',
		],
	];
}

function article_latest(int $limit = 9): array
{
	$limit = max(1, min($limit, 50));
	$pdo = db();

	if (!$pdo instanceof PDO) {
		return array_slice(article_fallback_data(), 0, $limit);
	}

	$sql = <<<SQL
		SELECT
			a.id,
			a.title,
			a.slug,
			a.excerpt,
			a.content,
			a.published_at,
			a.updated_at,
			c.name AS category_name,
			c.slug AS category_slug,
			img.file_path AS image_path,
			COALESCE(NULLIF(img.alt_text, ''), a.title) AS image_alt
		FROM articles a
		INNER JOIN categories c ON c.id = a.category_id
		LEFT JOIN LATERAL (
			SELECT ai.file_path, ai.alt_text
			FROM article_images ai
			WHERE ai.article_id = a.id
			ORDER BY ai.sort_order ASC, ai.id ASC
			LIMIT 1
		) img ON TRUE
		WHERE a.is_published = TRUE
		ORDER BY a.published_at DESC NULLS LAST, a.id DESC
		LIMIT :limit
	SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->execute();

	return $stmt->fetchAll() ?: [];
}

function article_find_by_slug(string $slug): ?array
{
	$slug = trim($slug);
	if ($slug === '') {
		return null;
	}

	$pdo = db();
	if (!$pdo instanceof PDO) {
		foreach (article_fallback_data() as $article) {
			if ($article['slug'] === $slug) {
				return $article;
			}
		}

		return null;
	}

	$sql = <<<SQL
		SELECT
			a.id,
			a.title,
			a.slug,
			a.excerpt,
			a.content,
			a.published_at,
			a.updated_at,
			c.name AS category_name,
			c.slug AS category_slug,
			img.file_path AS image_path,
			COALESCE(NULLIF(img.alt_text, ''), a.title) AS image_alt
		FROM articles a
		INNER JOIN categories c ON c.id = a.category_id
		LEFT JOIN LATERAL (
			SELECT ai.file_path, ai.alt_text
			FROM article_images ai
			WHERE ai.article_id = a.id
			ORDER BY ai.sort_order ASC, ai.id ASC
			LIMIT 1
		) img ON TRUE
		WHERE a.slug = :slug
		  AND a.is_published = TRUE
		LIMIT 1
	SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->execute(['slug' => $slug]);
	$result = $stmt->fetch();

	return is_array($result) ? $result : null;
}

function article_by_category_slug(string $categorySlug, int $limit = 20): array
{
	$categorySlug = trim($categorySlug);
	$limit = max(1, min($limit, 100));

	if ($categorySlug === '') {
		return [];
	}

	$pdo = db();
	if (!$pdo instanceof PDO) {
		$matches = array_values(array_filter(article_fallback_data(), static function (array $article) use ($categorySlug): bool {
			return $article['category_slug'] === $categorySlug;
		}));

		return array_slice($matches, 0, $limit);
	}

	$sql = <<<SQL
		SELECT
			a.id,
			a.title,
			a.slug,
			a.excerpt,
			a.content,
			a.published_at,
			a.updated_at,
			c.name AS category_name,
			c.slug AS category_slug,
			img.file_path AS image_path,
			COALESCE(NULLIF(img.alt_text, ''), a.title) AS image_alt
		FROM articles a
		INNER JOIN categories c ON c.id = a.category_id
		LEFT JOIN LATERAL (
			SELECT ai.file_path, ai.alt_text
			FROM article_images ai
			WHERE ai.article_id = a.id
			ORDER BY ai.sort_order ASC, ai.id ASC
			LIMIT 1
		) img ON TRUE
		WHERE c.slug = :slug
		  AND a.is_published = TRUE
		ORDER BY a.published_at DESC NULLS LAST, a.id DESC
		LIMIT :limit
	SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':slug', $categorySlug);
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->execute();

	return $stmt->fetchAll() ?: [];
}

function article_search(string $query, int $limit = 20): array
{
	$query = trim($query);
	$limit = max(1, min($limit, 100));

	if ($query === '') {
		return [];
	}

	$pdo = db();
	if (!$pdo instanceof PDO) {
		$needle = mb_strtolower($query);
		$matches = array_values(array_filter(article_fallback_data(), static function (array $article) use ($needle): bool {
			$haystack = mb_strtolower($article['title'] . ' ' . $article['excerpt'] . ' ' . $article['content']);
			return mb_strpos($haystack, $needle) !== false;
		}));

		return array_slice($matches, 0, $limit);
	}

	$sql = <<<SQL
		SELECT
			a.id,
			a.title,
			a.slug,
			a.excerpt,
			a.content,
			a.published_at,
			a.updated_at,
			c.name AS category_name,
			c.slug AS category_slug,
			img.file_path AS image_path,
			COALESCE(NULLIF(img.alt_text, ''), a.title) AS image_alt
		FROM articles a
		INNER JOIN categories c ON c.id = a.category_id
		LEFT JOIN LATERAL (
			SELECT ai.file_path, ai.alt_text
			FROM article_images ai
			WHERE ai.article_id = a.id
			ORDER BY ai.sort_order ASC, ai.id ASC
			LIMIT 1
		) img ON TRUE
		WHERE a.is_published = TRUE
		  AND (
			  a.title ILIKE :search
			  OR a.excerpt ILIKE :search
			  OR a.content ILIKE :search
		  )
		ORDER BY a.published_at DESC NULLS LAST, a.id DESC
		LIMIT :limit
	SQL;

	$stmt = $pdo->prepare($sql);
	$stmt->bindValue(':search', '%' . $query . '%');
	$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
	$stmt->execute();

	return $stmt->fetchAll() ?: [];
}

function article_related(string $categorySlug, string $excludeSlug, int $limit = 3): array
{
	$categorySlug = trim($categorySlug);
	$excludeSlug = trim($excludeSlug);
	$limit = max(1, min($limit, 12));

	if ($categorySlug === '') {
		return [];
	}

	$items = article_by_category_slug($categorySlug, 20);
	$items = array_values(array_filter($items, static function (array $article) use ($excludeSlug): bool {
		return $article['slug'] !== $excludeSlug;
	}));

	return array_slice($items, 0, $limit);
}

function article_sitemap_items(): array
{
	$pdo = db();

	if (!$pdo instanceof PDO) {
		return array_map(static function (array $article): array {
			return [
				'slug' => $article['slug'],
				'updated_at' => $article['updated_at'] ?? $article['published_at'] ?? date('Y-m-d H:i:s'),
			];
		}, article_fallback_data());
	}

	$stmt = $pdo->query(
		'SELECT slug, COALESCE(updated_at, published_at, created_at) AS updated_at FROM articles WHERE is_published = TRUE ORDER BY id DESC'
	);

	return $stmt->fetchAll() ?: [];
}
