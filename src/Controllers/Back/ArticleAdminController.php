<?php

declare(strict_types=1);

namespace Controllers\Back;

class ArticleAdminController
{
	public function index(): void
	{
		require __DIR__ . '/../../../templates/back/articles/list.php';
	}

	public function create(): void
	{
		require __DIR__ . '/../../../templates/back/articles/create.php';
	}

	public function store(): void
	{
		header('Location: /admin/articles');
		exit;
	}

	public function edit(string $id): void
	{
		require __DIR__ . '/../../../templates/back/articles/edit.php';
	}

	public function update(string $id): void
	{
		header('Location: /admin/articles');
		exit;
	}

	public function delete(string $id): void
	{
		header('Location: /admin/articles');
		exit;
	}

	public function togglePublish(string $id): void
	{
		header('Location: /admin/articles');
		exit;
	}
}

