<?php

namespace Core;

abstract class Controller
{
    protected function render(string $view, array $data = []): void
    {
        extract($data);
        
        $viewFile = __DIR__ . "/../../templates/back/{$view}.php";
        
        if (!file_exists($viewFile)) {
            throw new \Exception("Vue non trouvée: {$viewFile}");
        }
        
        $layoutFile = __DIR__ . "/../../templates/back/layouts/admin.php";
        
        if (file_exists($layoutFile)) {
            ob_start();
            require $viewFile;
            $content = ob_get_clean();
            
            require $layoutFile;
        } else {
            require $viewFile;
        }
    }
    
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
    
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/admin/login');
        }
    }
    
    protected function requireAdmin(): void
    {
        $this->requireAuth();
        
        if ($_SESSION['user_role'] !== 'admin') {
            $this->redirect('/admin/dashboard');
        }
    }
}