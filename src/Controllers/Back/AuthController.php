<?php

declare(strict_types=1);

namespace Controllers\Back;

class AuthController
{
	public function showLogin(): void
	{
		require __DIR__ . '/../../../templates/back/auth/login.php';
	}

	public function login(): void
	{
		header('Location: /admin/dashboard');
		exit;
	}

	public function logout(): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			$_SESSION = [];
			session_destroy();
		}

		header('Location: /admin/login');
		exit;
	}
}

