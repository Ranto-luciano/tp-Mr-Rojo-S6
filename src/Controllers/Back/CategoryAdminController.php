<?php

declare(strict_types=1);

namespace Controllers\Back;

use Core\Controller;
use Core\Auth;
use Models\Category;

class CategoryAdminController extends Controller
{
    private Category $categoryModel;
    
    public function __construct()
    {
        $this->requireAuth();
        $this->categoryModel = new Category();
    }
    
    public function index(): void
    {
        $categories = $this->categoryModel->findAll();
        
        $this->render('categories/list', [
            'categories' => $categories
        ]);
    }
    
    public function create(): void
    {
        $this->render('categories/create');
    }
    
    public function store(): void
    {
        $errors = $this->validateCategory($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/admin/categories/create');
            return;
        }
        
        try {
            $this->categoryModel->create([
                'name' => trim($_POST['name']),
                'slug' => !empty($_POST['slug']) ? trim($_POST['slug']) : null
            ]);
            
            $_SESSION['success'] = 'Catégorie créée avec succès';
            $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la création de la catégorie';
            $this->redirect('/admin/categories/create');
        }
    }
    
    public function edit(int $id): void
    {
        $category = $this->categoryModel->findById($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Catégorie non trouvée';
            $this->redirect('/admin/categories');
            return;
        }
        
        $this->render('categories/edit', [
            'category' => $category
        ]);
    }
    
    public function update(int $id): void
    {
        $category = $this->categoryModel->findById($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Catégorie non trouvée';
            $this->redirect('/admin/categories');
            return;
        }
        
        $errors = $this->validateCategory($_POST, $id);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/admin/categories/' . $id . '/edit');
            return;
        }
        
        try {
            $this->categoryModel->update($id, [
                'name' => trim($_POST['name']),
                'slug' => !empty($_POST['slug']) ? trim($_POST['slug']) : null
            ]);
            
            $_SESSION['success'] = 'Catégorie mise à jour avec succès';
            $this->redirect('/admin/categories');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
            $this->redirect('/admin/categories/' . $id . '/edit');
        }
    }
    
    public function delete(int $id): void
    {
        $category = $this->categoryModel->findById($id);
        
        if (!$category) {
            $_SESSION['error'] = 'Catégorie non trouvée';
            $this->redirect('/admin/categories');
            return;
        }
        
        try {
            $this->categoryModel->delete($id);
            $_SESSION['success'] = 'Catégorie supprimée avec succès';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        
        $this->redirect('/admin/categories');
    }
    
    private function validateCategory(array $data, ?int $id = null): array
    {
        $errors = [];
        
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Le nom est obligatoire';
        } elseif (strlen($data['name']) > 120) {
            $errors['name'] = 'Le nom ne peut pas dépasser 120 caractères';
        }
        
        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $errors['slug'] = 'Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets';
        }
        
        return $errors;
    }

	// public function index(): void
	// {
	// 	require __DIR__ . '/../../../templates/back/categories/list.php';
	// }

	// public function create(): void
	// {
	// 	require __DIR__ . '/../../../templates/back/categories/create.php';
	// }

	// public function store(): void
	// {
	// 	header('Location: /admin/categories');
	// 	exit;
	// }

	// public function edit(string $id): void
	// {
	// 	require __DIR__ . '/../../../templates/back/categories/edit.php';
	// }

	// public function update(string $id): void
	// {
	// 	header('Location: /admin/categories');
	// 	exit;
	// }

	// public function delete(string $id): void
	// {
	// 	header('Location: /admin/categories');
	// 	exit;
	// }
}