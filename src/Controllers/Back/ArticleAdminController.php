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
        $categories = $this->categoryModel->findAll();

        $search = trim((string)($_GET['search'] ?? ''));
        $status = (string)($_GET['status'] ?? '');
        $categoryFilter = (int)($_GET['category'] ?? 0);
        $currentPage = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;

        $filtered = array_values(array_filter($articles, static function (array $article) use ($search, $status, $categoryFilter): bool {
            if ($search !== '') {
                $haystack = mb_strtolower((string)(
                    ($article['title'] ?? '') . ' ' .
                    ($article['excerpt'] ?? '') . ' ' .
                    ($article['content'] ?? '')
                ));
                if (!str_contains($haystack, mb_strtolower($search))) {
                    return false;
                }
            }

            if ($status === 'published' && empty($article['is_published'])) {
                return false;
            }

            if ($status === 'draft' && !empty($article['is_published'])) {
                return false;
            }

            if ($categoryFilter > 0 && (int)($article['category_id'] ?? 0) !== $categoryFilter) {
                return false;
            }

            return true;
        }));

        $totalItems = count($filtered);
        $totalPages = max(1, (int)ceil($totalItems / $perPage));
        $currentPage = min($currentPage, $totalPages);
        $offset = ($currentPage - 1) * $perPage;
        $pageItems = array_slice($filtered, $offset, $perPage);

        foreach ($pageItems as &$article) {
            $article['images'] = $this->articleModel->getImages((int)$article['id']);
            $article['views_count'] = $article['views'] ?? 0;
        }
        unset($article);
        
        $this->render('articles/list', [
            'articles' => $pageItems,
            'categories' => $categories,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
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
        
        if (!empty($_FILES['featured_image']['name'])) {
            $this->uploadImage($articleId, $_FILES['featured_image'], $_POST['featured_image_alt'] ?? null, 0);
        } elseif (!empty($_FILES['image']['name'])) {
            $this->uploadImage($articleId, $_FILES['image'], $_POST['image_alt'] ?? null, 0);
        }

        $galleryFiles = $this->normalizeUploadedFiles($_FILES['images'] ?? null);
        if ($galleryFiles !== []) {
            $galleryAlts = $_POST['image_alts'] ?? [];
            $startOrder = $this->articleModel->getMaxImageSortOrder($articleId) + 1;
            $this->uploadGalleryImages($articleId, $galleryFiles, $galleryAlts, $startOrder);
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

        if (!empty($_POST['delete_featured'])) {
            $this->deleteFeaturedImage($id);
        }

        $deleteIds = $_POST['delete_images'] ?? [];
        if (is_array($deleteIds)) {
            foreach ($deleteIds as $imageId) {
                $this->deleteImageById($id, (int) $imageId);
            }
        }

        $imageAlts = $_POST['image_alts'] ?? [];
        if (is_array($imageAlts)) {
            foreach ($imageAlts as $imageId => $altText) {
                $this->updateImageAlt($id, (int) $imageId, (string) $altText);
            }
        }
        
        if (!empty($_FILES['featured_image']['name'])) {
            $this->deleteFeaturedImage($id);
            $this->uploadImage($id, $_FILES['featured_image'], $_POST['featured_image_alt'] ?? null, 0);
        } elseif (!empty($_FILES['image']['name'])) {
            $this->uploadImage($id, $_FILES['image'], $_POST['image_alt'] ?? null, 0);
        }

        $newImages = $this->normalizeUploadedFiles($_FILES['new_images'] ?? null);
        if ($newImages !== []) {
            $newAlts = $_POST['new_image_alts'] ?? [];
            $startOrder = $this->articleModel->getMaxImageSortOrder($id) + 1;
            $this->uploadGalleryImages($id, $newImages, $newAlts, $startOrder);
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
    
    private function uploadImage(int $articleId, array $file, ?string $altText = null, int $sortOrder = 0): void
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
            $altText = $altText ?: pathinfo($file['name'], PATHINFO_FILENAME);
			
            $this->articleModel->addImage($articleId, $relativePath, $altText, $sortOrder);
            $_SESSION['success'] = 'Image ajoutée avec succès';
        } else {
            $_SESSION['error'] = 'Erreur lors de l\'upload de l\'image';
        }
    }

    private function normalizeUploadedFiles(?array $files): array
    {
        if (!$files || !is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $normalized = [];
        foreach ($files['name'] as $index => $name) {
            if ($name === '' || !isset($files['tmp_name'][$index])) {
                continue;
            }
            $normalized[] = [
                'name' => $name,
                'type' => $files['type'][$index] ?? '',
                'tmp_name' => $files['tmp_name'][$index] ?? '',
                'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$index] ?? 0,
            ];
        }

        return $normalized;
    }

    private function uploadGalleryImages(int $articleId, array $files, array $alts, int $startOrder): void
    {
        $sortOrder = $startOrder;
        foreach ($files as $index => $file) {
            if (!empty($file['error'])) {
                continue;
            }
            $altText = $alts[$index] ?? null;
            $this->uploadImage($articleId, $file, $altText, $sortOrder);
            $sortOrder++;
        }
    }

    private function deleteFeaturedImage(int $articleId): void
    {
        $images = $this->articleModel->getImages($articleId);
        if ($images === []) {
            return;
        }

        $featured = $images[0];
        $this->deleteImageById($articleId, (int) $featured['id']);
    }

    private function deleteImageById(int $articleId, int $imageId): void
    {
        $image = $this->articleModel->getImageById($imageId);
        if (!$image || (int) $image['article_id'] !== $articleId) {
            return;
        }

        $filePath = __DIR__ . '/../../../storage/uploads/' . $image['file_path'];
        if (is_file($filePath)) {
            unlink($filePath);
        }

        $this->articleModel->deleteImageById($imageId);
    }

    private function updateImageAlt(int $articleId, int $imageId, string $altText): void
    {
        $image = $this->articleModel->getImageById($imageId);
        if (!$image || (int) $image['article_id'] !== $articleId) {
            return;
        }

        $cleanAlt = trim($altText);
        if ($cleanAlt === '') {
            return;
        }

        $this->articleModel->updateImageAlt($imageId, $cleanAlt);
    }
    
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}

