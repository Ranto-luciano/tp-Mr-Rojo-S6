<?php
/**
 * Modèle Utilisateur - Version complète
 */

namespace Models;

use Core\Database;
use PDO;

class User
{
    private PDO $db;
    
    public function __construct()
    {
        $this->db = Database::getConnection();
    }
    
    /**
     * Trouve tous les utilisateurs
     */
    public function findAll(): array
    {
        $stmt = $this->db->query("
            SELECT u.*, COUNT(a.id) as articles_count
            FROM users u
            LEFT JOIN articles a ON a.author_id = u.id
            GROUP BY u.id
            ORDER BY u.created_at DESC
        ");
        
        return $stmt->fetchAll();
    }
    
    /**
     * Trouve un utilisateur par son ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Trouve un utilisateur par son email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create(array $data): int
    {
        $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users (full_name, email, password_hash, role)
            VALUES (:full_name, :email, :password_hash, :role)
            RETURNING id
        ");
        
        $stmt->execute([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password_hash' => $passwordHash,
            'role' => $data['role'] ?? 'editor'
        ]);
        
        return $stmt->fetch()['id'];
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update(int $id, array $data): bool
    {
        $fields = [];
        $params = ['id' => $id];
        
        if (isset($data['full_name'])) {
            $fields[] = "full_name = :full_name";
            $params['full_name'] = $data['full_name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params['email'] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $fields[] = "password_hash = :password_hash";
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params['role'] = $data['role'];
        }
        
        $fields[] = "updated_at = NOW()";
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute($params);
    }
    
    /**
     * Supprime un utilisateur
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
    
    /**
     * Enregistre la dernière connexion
     */
    public function updateLastLogin(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET updated_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}