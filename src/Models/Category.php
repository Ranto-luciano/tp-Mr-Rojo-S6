<?php

namespace Models;

use Core\Database;
use PDO;

class Category
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(a.id) as articles_count
            FROM categories c
            LEFT JOIN articles a ON a.category_id = c.id
            GROUP BY c.id
            ORDER BY c.name ASC
        ");
        
        return $stmt->fetchAll();
    }
    
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch();
        
        return $category ?: null;
    }
    
    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = :slug");
        $stmt->execute(['slug' => $slug]);
        $category = $stmt->fetch();
        
        return $category ?: null;
    }
    
    public function create(array $data): int
    {
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, slug)
            VALUES (:name, :slug)
            RETURNING id
        ");
        
        $stmt->execute([
            'name' => $data['name'],
            'slug' => $data['slug']
        ]);
        
        return $stmt->fetch()['id'];
    }
    
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        
        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params['name'] = $data['name'];
            
            if (empty($data['slug'])) {
                $fields[] = "slug = :slug";
                $params['slug'] = $this->generateSlug($data['name']);
            }
        }
        
        if (isset($data['slug'])) {
            $fields[] = "slug = :slug";
            $params['slug'] = $data['slug'];
        }
        
        $fields[] = "updated_at = NOW()";
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE categories SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE category_id = :id");
        $stmt->execute(['id' => $id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            throw new \Exception("Impossible de supprimer une catégorie qui contient des articles");
        }
        
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    private function generateSlug(string $name): string
    {
        $slug = mb_strtolower($name);
        
        $slug = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'],
            ['a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'],
            $slug
        );
        
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        
        $slug = trim($slug, '-');
        
        return $slug;
    }
}