<?php

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

    public function findAll(int $limit = null, int $offset = 0): array
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
}
