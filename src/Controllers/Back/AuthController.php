<?php
declare(strict_types=1);

namespace Controllers\Back;

use Core\Controller;
use Core\Auth;
use Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin/dashboard');
        }
        
        $this->render('auth/login');
    }
    
    public function login(): void
    {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $_SESSION['error'] = 'Veuillez remplir tous les champs';
            $this->redirect('/admin/login');
            return;
        }
        
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        
        if (Auth::attempt($email, $password)) {
            $_SESSION['success'] = 'Bienvenue ' . $_SESSION['user_name'];
            $this->redirect('/admin/dashboard');
        } else {
            $_SESSION['error'] = 'Email ou mot de passe incorrect';
            $this->redirect('/admin/login');
        }
    }
    
    public function logout(): void
    {
        Auth::logout();
        $_SESSION['success'] = 'Vous avez été déconnecté';
        $this->redirect('/admin/login');
    }

	// public function showLogin(): void
	// {
	// 	require __DIR__ . '/../../../templates/back/auth/login.php';
	// }

	// public function login(): void
	// {
	// 	header('Location: /admin/dashboard');
	// 	exit;
	// }

	// public function logout(): void
	// {
	// 	if (session_status() === PHP_SESSION_ACTIVE) {
	// 		$_SESSION = [];
	// 		session_destroy();
	// 	}

	// 	header('Location: /admin/login');
	// 	exit;
	// }
}