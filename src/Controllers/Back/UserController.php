<?php
declare(strict_types=1);

namespace Controllers\Back;

use Core\Controller;
use Core\Auth;
use Models\User;
use Models\Article;

class UserController extends Controller
{
    private User $userModel;
    private Article $articleModel;
    
    public function __construct()
    {
        $this->requireAdmin(); // Seul l'admin peut gérer les utilisateurs
        $this->userModel = new User();
        $this->articleModel = new Article();
    }
    
    /**
     * Liste tous les utilisateurs
     */
    public function index(): void
    {
        // Récupération des utilisateurs avec leurs statistiques
        $users = $this->userModel->findAll();
        
        $this->render('users/list', [
            'users' => $users
        ]);
    }
    
    /**
     * Affiche le formulaire de création d'utilisateur
     */
    public function create(): void
    {
        $this->render('users/create');
    }
    
    /**
     * Enregistre un nouvel utilisateur
     */
    public function store(): void
    {
        $errors = $this->validateUser($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $_POST;
            $this->redirect('/admin/users/create');
            return;
        }
        
        try {
            $this->userModel->create([
                'full_name' => trim($_POST['full_name']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'role' => $_POST['role'] ?? 'editor'
            ]);
            
            $_SESSION['success'] = 'Utilisateur créé avec succès';
            $this->redirect('/admin/users');
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la création de l\'utilisateur';
            $this->redirect('/admin/users/create');
        }
    }
    
    /**
     * Affiche le formulaire d'édition d'utilisateur
     */
    public function edit(int $id): void
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('/admin/users');
            return;
        }
        
        $this->render('users/edit', [
            'user' => $user
        ]);
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update(int $id): void
    {
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('/admin/users');
            return;
        }
        
        $data = [];
        
        if (!empty($_POST['full_name'])) {
            $data['full_name'] = trim($_POST['full_name']);
        }
        
        if (!empty($_POST['email'])) {
            $data['email'] = trim($_POST['email']);
        }
        
        if (!empty($_POST['role'])) {
            $data['role'] = $_POST['role'];
        }
        
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        try {
            $this->userModel->update($id, $data);
            $_SESSION['success'] = 'Utilisateur mis à jour avec succès';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour';
        }
        
        $this->redirect('/admin/users');
    }
    
    /**
     * Supprime un utilisateur
     */
    public function delete(int $id): void
    {
        // Empêche la suppression de son propre compte
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error'] = 'Vous ne pouvez pas supprimer votre propre compte';
            $this->redirect('/admin/users');
            return;
        }
        
        $user = $this->userModel->findById($id);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('/admin/users');
            return;
        }
        
        // Vérifie si l'utilisateur a des articles
        $articles = $this->articleModel->findByAuthor($id);
        if (count($articles) > 0) {
            $_SESSION['error'] = 'Impossible de supprimer un utilisateur qui a publié des articles';
            $this->redirect('/admin/users');
            return;
        }
        
        try {
            $this->userModel->delete($id);
            $_SESSION['success'] = 'Utilisateur supprimé avec succès';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la suppression';
        }
        
        $this->redirect('/admin/users');
    }
    
    /**
     * Affiche le profil de l'utilisateur connecté
     */
    public function profile(): void
    {
        $user = Auth::user();
        
        // Récupère le nombre d'articles publiés par l'utilisateur
        $articlesCount = $this->articleModel->countByAuthor($user['id']);
        
        $this->render('users/profile', [
            'user' => $user,
            'articles_count' => $articlesCount
        ]);
    }
    
    /**
     * Met à jour le profil
     */
    public function updateProfile(): void
    {
        $userId = $_SESSION['user_id'];
        
        $data = [];
        
        if (!empty($_POST['full_name'])) {
            $data['full_name'] = trim($_POST['full_name']);
        }
        
        if (!empty($_POST['email'])) {
            $data['email'] = trim($_POST['email']);
        }
        
        try {
            $this->userModel->update($userId, $data);
            $_SESSION['success'] = 'Profil mis à jour avec succès';
            
            // Mise à jour de la session
            $_SESSION['user_name'] = $data['full_name'] ?? $_SESSION['user_name'];
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la mise à jour du profil';
        }
        
        $this->redirect('/admin/users/profile');
    }
    
    /**
     * Change le mot de passe
     */
    public function changePassword(): void
    {
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->findById($userId);
        
        if (!$user) {
            $_SESSION['error'] = 'Utilisateur non trouvé';
            $this->redirect('/admin/users/profile');
            return;
        }
        
        // Vérifie le mot de passe actuel
        if (!password_verify($_POST['current_password'], $user['password_hash'])) {
            $_SESSION['error'] = 'Mot de passe actuel incorrect';
            $this->redirect('/admin/users/profile');
            return;
        }
        
        // Vérifie la confirmation
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            $_SESSION['error'] = 'Les mots de passe ne correspondent pas';
            $this->redirect('/admin/users/profile');
            return;
        }
        
        // Vérifie la longueur
        if (strlen($_POST['new_password']) < 8) {
            $_SESSION['error'] = 'Le mot de passe doit contenir au moins 8 caractères';
            $this->redirect('/admin/users/profile');
            return;
        }
        
        try {
            $this->userModel->update($userId, ['password' => $_POST['new_password']]);
            $_SESSION['success'] = 'Mot de passe modifié avec succès';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Erreur lors de la modification du mot de passe';
        }
        
        $this->redirect('/admin/users/profile');
    }
    
    /**
     * Valide les données d'un utilisateur
     */
    private function validateUser(array $data): array
    {
        $errors = [];
        
        if (empty(trim($data['full_name'] ?? ''))) {
            $errors['full_name'] = 'Le nom est obligatoire';
        }
        
        if (empty(trim($data['email'] ?? ''))) {
            $errors['email'] = 'L\'email est obligatoire';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email invalide';
        } else {
            // Vérifie si l'email existe déjà
            $existingUser = $this->userModel->findByEmail($data['email']);
            if ($existingUser && (!isset($data['id']) || $existingUser['id'] != $data['id'])) {
                $errors['email'] = 'Cet email est déjà utilisé';
            }
        }
        
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 8) {
                $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères';
            }
        } elseif (!isset($data['id'])) {
            // Si création d'un nouvel utilisateur, mot de passe obligatoire
            $errors['password'] = 'Le mot de passe est obligatoire';
        }
        
        return $errors;
    }

	// public function index(): void
	// {
	// 	$this->placeholder('Liste des utilisateurs');
	// }

	// public function create(): void
	// {
	// 	$this->placeholder('Creation utilisateur');
	// }

	// public function store(): void
	// {
	// 	header('Location: /admin/users');
	// 	exit;
	// }

	// public function edit(string $id): void
	// {
	// 	$this->placeholder('Edition utilisateur #' . $id);
	// }

	// public function update(string $id): void
	// {
	// 	header('Location: /admin/users');
	// 	exit;
	// }

	// public function delete(string $id): void
	// {
	// 	header('Location: /admin/users');
	// 	exit;
	// }

	// public function profile(): void
	// {
	// 	require __DIR__ . '/../../../templates/back/users/profile.php';
	// }

	// public function updateProfile(): void
	// {
	// 	header('Location: /admin/users/profile');
	// 	exit;
	// }

	// public function changePassword(): void
	// {
	// 	header('Location: /admin/users/profile');
	// 	exit;
	// }

	// private function placeholder(string $title): void
	// {
	// 	echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>'
	// 		. htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
	// 		. '</title></head><body><h1>'
	// 		. htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
	// 		. '</h1><p>Cette page sera completee dans la partie BackOffice.</p></body></html>';
	// }
}