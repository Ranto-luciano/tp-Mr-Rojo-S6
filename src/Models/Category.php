<?php

declare(strict_types=1);

function category_all_with_counts(): array
{
	$pdo = db();
	if (!$pdo instanceof PDO) {
		return [
			['name' => 'Politique', 'slug' => 'politique', 'article_count' => 2],
			['name' => 'Diplomatie', 'slug' => 'diplomatie', 'article_count' => 1],
			['name' => 'Economie', 'slug' => 'economie', 'article_count' => 2],
			['name' => 'Securite', 'slug' => 'securite', 'article_count' => 1],
			['name' => 'Societe', 'slug' => 'societe', 'article_count' => 1],
		];
	}

	$sql = <<<SQL
		SELECT c.name, c.slug, COUNT(a.id) AS article_count
		FROM categories c
		LEFT JOIN articles a ON a.category_id = c.id AND a.is_published = TRUE
		GROUP BY c.id
		ORDER BY c.name ASC
	SQL;

	return $pdo->query($sql)->fetchAll() ?: [];
}

function category_find_by_slug(string $slug): ?array
{
	$slug = trim($slug);
	if ($slug === '') {
		return null;
	}

	$pdo = db();
	if (!$pdo instanceof PDO) {
		foreach (category_all_with_counts() as $category) {
			if ($category['slug'] === $slug) {
				return $category;
			}
		}

		return null;
	}

	$stmt = $pdo->prepare('SELECT id, name, slug FROM categories WHERE slug = :slug LIMIT 1');
	$stmt->execute(['slug' => $slug]);
	$result = $stmt->fetch();

	return is_array($result) ? $result : null;
}
