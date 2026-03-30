<?php

declare(strict_types=1);

namespace Controllers\Back;

class CategoryAdminController
{
	public function index(): void
	{
		require __DIR__ . '/../../../templates/back/categories/list.php';
	}

	public function create(): void
	{
		require __DIR__ . '/../../../templates/back/categories/create.php';
	}

	public function store(): void
	{
		header('Location: /admin/categories');
		exit;
	}

	public function edit(string $id): void
	{
		require __DIR__ . '/../../../templates/back/categories/edit.php';
	}

	public function update(string $id): void
	{
		header('Location: /admin/categories');
		exit;
	}

	public function delete(string $id): void
	{
		header('Location: /admin/categories');
		exit;
	}
}

