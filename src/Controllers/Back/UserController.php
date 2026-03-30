<?php

declare(strict_types=1);

namespace Controllers\Back;

class UserController
{
	public function index(): void
	{
		$this->placeholder('Liste des utilisateurs');
	}

	public function create(): void
	{
		$this->placeholder('Creation utilisateur');
	}

	public function store(): void
	{
		header('Location: /admin/users');
		exit;
	}

	public function edit(string $id): void
	{
		$this->placeholder('Edition utilisateur #' . $id);
	}

	public function update(string $id): void
	{
		header('Location: /admin/users');
		exit;
	}

	public function delete(string $id): void
	{
		header('Location: /admin/users');
		exit;
	}

	public function profile(): void
	{
		require __DIR__ . '/../../../templates/back/users/profile.php';
	}

	public function updateProfile(): void
	{
		header('Location: /admin/users/profile');
		exit;
	}

	public function changePassword(): void
	{
		header('Location: /admin/users/profile');
		exit;
	}

	private function placeholder(string $title): void
	{
		echo '<!doctype html><html lang="fr"><head><meta charset="utf-8"><title>'
			. htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
			. '</title></head><body><h1>'
			. htmlspecialchars($title, ENT_QUOTES, 'UTF-8')
			. '</h1><p>Cette page sera completee dans la partie BackOffice.</p></body></html>';
	}
}

