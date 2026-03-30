<?php
declare(strict_types=1);

namespace Controllers\Back;

use Core\Controller;
use Core\Auth;
use Models\Article;
use Models\Category;

class ArticleAdminController extends Controller
{
    private Article $articleModel;
    private Category $categoryModel;
    
    public function __construct()
    {
        $this->requireAuth();
        $this->articleModel = new Article();
        $this->categoryModel = new Category();
    }
    
    public function index(): void
    {
        $articles = $this->articleModel->findAll();
        
        $this->render('articles/list', [
            'articles' => $articles
        ]);
    }
    
    public function create(): void
    {
        $categories = $this->categoryModel->findAll();
        
        $this->render('articles/create', [
            'categories' => $categories
        ]);
    }
    
    public function store(): void
    {
        $errors = $this->validateArticle($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/admin/articles/create');
            return;
        }
        
        $data = [
            'title' => trim($_POST['title']),
            'slug' => !empty($_POST['slug']) ? trim($_POST['slug']) : null,
            'category_id' => (int)$_POST['category_id'],
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'content' => trim($_POST['content']),
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'is_published' => isset($_POST['is_published']) && $_POST['is_published'] == '1'
        ];
        
        $articleId = $this->articleModel->create($data, $_SESSION['user_id']);
        
        if (!empty($_FILES['image']['name'])) {
            $this->uploadImage($articleId, $_FILES['image']);
        }
        
        $_SESSION['success'] = 'Article créé avec succès';
        $this->redirect('/admin/articles');
    }
    
    public function edit(int $id): void
    {
        $article = $this->articleModel->findById($id);
        
        if (!$article) {
            $_SESSION['error'] = 'Article non trouvé';
            $this->redirect('/admin/articles');
            return;
        }
        
        $categories = $this->categoryModel->findAll();
        
        $this->render('articles/edit', [
            'article' => $article,
            'categories' => $categories
        ]);
    }
    
    public function update(int $id): void
    {
        $article = $this->articleModel->findById($id);
        
        if (!$article) {
            $_SESSION['error'] = 'Article non trouvé';
            $this->redirect('/admin/articles');
            return;
        }
        
        $errors = $this->validateArticle($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/admin/articles/' . $id . '/edit');
            return;
        }
        
        $data = [
            'title' => trim($_POST['title']),
            'slug' => !empty($_POST['slug']) ? trim($_POST['slug']) : null,
            'category_id' => (int)$_POST['category_id'],
            'excerpt' => trim($_POST['excerpt'] ?? ''),
            'content' => trim($_POST['content']),
            'meta_title' => trim($_POST['meta_title'] ?? ''),
            'meta_description' => trim($_POST['meta_description'] ?? ''),
            'is_published' => isset($_POST['is_published']) && $_POST['is_published'] == '1'
        ];
        
        $this->articleModel->update($id, $data);
        
        if (!empty($_FILES['image']['name'])) {
            $this->uploadImage($id, $_FILES['image']);
        }
        
        $_SESSION['success'] = 'Article mis à jour avec succès';
        $this->redirect('/admin/articles');
    }
    
    public function delete(int $id): void
    {
        $article = $this->articleModel->findById($id);
        
        if (!$article) {
            $_SESSION['error'] = 'Article non trouvé';
            $this->redirect('/admin/articles');
            return;
        }
        
        foreach ($article['images'] as $image) {
            $filePath = __DIR__ . '/../../../storage/uploads/' . $image['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
        
        $this->articleModel->delete($id);
        
        $_SESSION['success'] = 'Article supprimé avec succès';
        $this->redirect('/admin/articles');
    }
    
    public function togglePublish(int $id): void
    {
        $this->articleModel->togglePublish($id);
        
        if ($this->isAjax()) {
            $this->json(['success' => true]);
        } else {
            $this->redirect('/admin/articles');
        }
    }
    
    private function validateArticle(array $data): array
    {
        $errors = [];
        
        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = 'Le titre est obligatoire';
        } elseif (strlen($data['title']) > 220) {
            $errors['title'] = 'Le titre ne peut pas dépasser 220 caractères';
        }
        
        if (empty($data['category_id']) || $data['category_id'] <= 0) {
            $errors['category_id'] = 'La catégorie est obligatoire';
        }
        
        if (empty(trim($data['content'] ?? ''))) {
            $errors['content'] = 'Le contenu est obligatoire';
        }
        
        if (!empty($data['slug']) && !preg_match('/^[a-z0-9-]+$/', $data['slug'])) {
            $errors['slug'] = 'Le slug ne peut contenir que des lettres minuscules, des chiffres et des tirets';
        }
        
        if (!empty($data['meta_description']) && strlen($data['meta_description']) > 320) {
            $errors['meta_description'] = 'La méta-description ne peut pas dépasser 320 caractères';
        }
        
        return $errors;
    }
    
    private function uploadImage(int $articleId, array $file): void
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; 
        
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['warning'] = 'Format d\'image non supporté (JPEG, PNG, WebP uniquement)';
            return;
        }
        
        if ($file['size'] > $maxSize) {
            $_SESSION['warning'] = 'L\'image ne doit pas dépasser 5 MB';
            return;
        }

        $uploadDir = __DIR__ . '/../../../storage/uploads/articles/' . $articleId . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filePath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $relativePath = 'articles/' . $articleId . '/' . $filename;
            $altText = $_POST['image_alt'] ?? pathinfo($file['name'], PATHINFO_FILENAME);
            
            $this->articleModel->addImage($articleId, $relativePath, $altText);
            $_SESSION['success'] = 'Image ajoutée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'upload de l\'image';
        }
    }
    
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

	// public function index(): void
	// {
	// 	require __DIR__ . '/../../../templates/back/articles/list.php';
	// }

	// public function create(): void
	// {
	// 	require __DIR__ . '/../../../templates/back/articles/create.php';
	// }

	// public function store(): void
	// {
	// 	header('Location: /admin/articles');
	// 	exit;
	// }

	// public function edit(string $id): void
	// {
	// 	require __DIR__ . '/../../../templates/back/articles/edit.php';
	// }

	// public function update(string $id): void
	// {
	// 	header('Location: /admin/articles');
	// 	exit;
	// }

	// public function delete(string $id): void
	// {
	// 	header('Location: /admin/articles');
	// 	exit;
	// }

	// public function togglePublish(string $id): void
	// {
	// 	header('Location: /admin/articles');
	// 	exit;
	// }
}

