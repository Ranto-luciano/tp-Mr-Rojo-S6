<?php

declare(strict_types=1);

namespace Models;

use Core\Database;
use PDO;

class Article
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $sql = "
            SELECT 
                a.*,
                c.name as category_name,
                c.slug as category_slug,
                u.full_name as author_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            ORDER BY a.created_at DESC
        ";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare($sql);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.*,
                c.name as category_name,
                c.slug as category_slug,
                u.full_name as author_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.id = :id
        ");

        $stmt->execute(['id' => $id]);
        $article = $stmt->fetch();

        if ($article) {
            $article['images'] = $this->getImages($id);
        }

        return $article ?: null;
    }

    public function getImages(int $articleId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM article_images 
            WHERE article_id = :article_id 
            ORDER BY sort_order ASC
        ");

        $stmt->execute(['article_id' => $articleId]);
        return $stmt->fetchAll();
    }

    public function create(array $data, int $authorId): int
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        }

        $stmt = $this->db->prepare("
            INSERT INTO articles (
                title, slug, category_id, author_id, excerpt, content,
                meta_title, meta_description, is_published, published_at
            ) VALUES (
                :title, :slug, :category_id, :author_id, :excerpt, :content,
                :meta_title, :meta_description, :is_published, 
                CASE WHEN :is_published THEN NOW() ELSE NULL END
            )
            RETURNING id
        ");

        $stmt->execute([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'category_id' => $data['category_id'],
            'author_id' => $authorId,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'is_published' => isset($data['is_published']) && $data['is_published'] ? 1 : 0
        ]);

        return $stmt->fetch()['id'];
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];

        $updatableFields = [
            'title',
            'slug',
            'category_id',
            'excerpt',
            'content',
            'meta_title',
            'meta_description',
            'is_published'
        ];

        foreach ($updatableFields as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (isset($data['is_published'])) {
            if ($data['is_published']) {
                $fields[] = "published_at = COALESCE(published_at, NOW())";
            } else {
                $fields[] = "published_at = NULL";
            }
        }

        $fields[] = "updated_at = NOW()";

        if (empty($fields)) {
            return false;
        }

        $sql = "UPDATE articles SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function togglePublish(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE articles 
            SET is_published = NOT is_published,
                published_at = CASE 
                    WHEN NOT is_published THEN NOW()
                    ELSE NULL
                END,
                updated_at = NOW()
            WHERE id = :id
            RETURNING is_published
        ");

        $stmt->execute(['id' => $id]);
        return $stmt->fetch() !== false;
    }

    public function addImage(int $articleId, string $filePath, string $altText, int $sortOrder = 0): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO article_images (article_id, file_path, alt_text, sort_order)
            VALUES (:article_id, :file_path, :alt_text, :sort_order)
            RETURNING id
        ");

        $stmt->execute([
            'article_id' => $articleId,
            'file_path' => $filePath,
            'alt_text' => $altText,
            'sort_order' => $sortOrder
        ]);

        return $stmt->fetch()['id'];
    }

    private function generateSlug(string $title): string
    {
        $slug = strtolower($title);
        $slug = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', ' '],
            ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', '-'],
            $slug
        );
        $slug = preg_replace('/[^a-z0-9-]+/', '', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists(string $slug): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        return $stmt->fetchColumn() > 0;
    }

    public function findByAuthor(int $authorId): array
    {
        $stmt = $this->db->prepare("
                SELECT * FROM articles 
                WHERE author_id = :author_id 
                ORDER BY created_at DESC
            ");

        $stmt->execute(['author_id' => $authorId]);
        return $stmt->fetchAll();
    }

    /**
     * Compte les articles par auteur
     */
    public function countByAuthor(int $authorId): int
    {
        $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM articles WHERE author_id = :author_id
            ");

        $stmt->execute(['author_id' => $authorId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Recherche d'articles
     */
    public function search(string $query, array $filters = []): array
    {
        $sql = "
                SELECT a.*, c.name as category_name, u.full_name as author_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                LEFT JOIN users u ON a.author_id = u.id
                WHERE (a.title ILIKE :query OR a.content ILIKE :query OR a.excerpt ILIKE :query)
            ";

        $params = ['query' => "%$query%"];

        if (!empty($filters['category_id'])) {
            $sql .= " AND a.category_id = :category_id";
            $params['category_id'] = $filters['category_id'];
        }

        if (isset($filters['is_published'])) {
            $sql .= " AND a.is_published = :is_published";
            $params['is_published'] = $filters['is_published'] ? 1 : 0;
        }

        $sql .= " ORDER BY a.created_at DESC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $filters['limit'];
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Met à jour les vues d'un article
     */
    public function incrementViews(int $id): void
    {
        $stmt = $this->db->prepare("
                UPDATE articles SET views = COALESCE(views, 0) + 1 WHERE id = :id
            ");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Récupère les articles populaires
     */
    public function getPopular(int $limit = 5): array
    {
        $stmt = $this->db->prepare("
                SELECT a.*, c.name as category_name
                FROM articles a
                LEFT JOIN categories c ON a.category_id = c.id
                WHERE a.is_published = true
                ORDER BY COALESCE(a.views, 0) DESC, a.published_at DESC
                LIMIT :limit
            ");

        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

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
        $pdo = $this->db;

        if (!$pdo instanceof PDO) {
            return array_slice($this->article_fallback_data(), 0, $limit);
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

        if (!$this->db instanceof PDO) {
            foreach ($this->article_fallback_data() as $article) {
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
		AND a.is_published = TRUE LIMIT 1
	SQL;

        $stmt = $this->db->prepare($sql);
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

        if (!$this->db instanceof PDO) {
            $matches = array_values(array_filter($this->article_fallback_data(), static function (array $article) use ($categorySlug): bool {
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
		ORDER BY a.published_at DESC NULLS LAST, a.id DESC LIMIT :limit
	SQL;

        $stmt = $this->db->prepare($sql);
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

        $pdo = $this->db;
        if (!$pdo instanceof PDO) {
            $needle = mb_strtolower($query);
            $matches = array_values(array_filter($this->article_fallback_data(), static function (array $article) use ($needle): bool {
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
            AND ( a.title ILIKE :search OR a.excerpt ILIKE :search OR a.content ILIKE :search )
            ORDER BY a.published_at DESC NULLS LAST, a.id DESC LIMIT :limit
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

        $items = $this->article_by_category_slug($categorySlug, 20);
        $items = array_values(array_filter($items, static function (array $article) use ($excludeSlug): bool {
            return $article['slug'] !== $excludeSlug;
        }));

        return array_slice($items, 0, $limit);
    }

    function article_sitemap_items(): array
    {
        $pdo = $this->db;

        if (!$pdo instanceof PDO) {
            return array_map(static function (array $article): array {
                return [
                    'slug' => $article['slug'],
                    'updated_at' => $article['updated_at'] ?? $article['published_at'] ?? date('Y-m-d H:i:s'),
                ];
            }, $this->article_fallback_data());
        }

        $stmt = $pdo->query(
            'SELECT slug, COALESCE(updated_at, published_at, created_at) AS updated_at FROM articles WHERE is_published = TRUE ORDER BY id DESC'
        );

        return $stmt->fetchAll() ?: [];
    }
}
