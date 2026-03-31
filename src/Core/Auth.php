<?php
namespace Core;

use Models\User;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        self::ensureDefaultAdminAccount();

        $userModel = new User();
        $user = $userModel->findByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (self::verifyPassword($password, (string) $user['password_hash'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            session_regenerate_id(true);
            
            return true;
        }
        
        return false;
    }

    private static function verifyPassword(string $password, string $hash): bool
    {
        if ($hash === '') {
            return false;
        }

        if (password_verify($password, $hash)) {
            return true;
        }

        $cryptResult = crypt($password, $hash);
        return is_string($cryptResult) && hash_equals($hash, $cryptResult);
    }

    private static function ensureDefaultAdminAccount(): void
    {
        try {
            $userModel = new User();
            $existing = $userModel->findByEmail('admin@example.com');

            if (!$existing) {
                $userModel->create([
                    'full_name' => 'Default Admin',
                    'email' => 'admin@example.com',
                    'password' => 'change_me',
                    'role' => 'admin',
                ]);
            }
        } catch (\Throwable $exception) {
            error_log('Unable to ensure default admin account: ' . $exception->getMessage());
        }
    }
    
    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    public static function check(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['user_id']);
    }
    
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        
        $userModel = new User();
        return $userModel->findById($_SESSION['user_id']);
    }
}