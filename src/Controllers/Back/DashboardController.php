<?php

declare(strict_types=1);

namespace Controllers\Back;

use Core\Controller;
use Models\Article;
use Models\Category;
use Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->requireAuth();
    }
    
    public function index(): void
    {
        $articleModel = new Article();
        $categoryModel = new Category();
        $userModel = new User();
        
        $stats = [
            'total_articles' => count($articleModel->findAll()),
            'total_categories' => count($categoryModel->findAll()),
            'total_users' => count($userModel->findAll()),
            'recent_articles' => $articleModel->findAll(5)
        ];
        
        $this->render('dashboard', [
            'stats' => $stats,
            'user_name' => $_SESSION['user_name']
        ]);
    }
}
