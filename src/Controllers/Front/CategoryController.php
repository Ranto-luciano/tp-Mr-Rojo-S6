<?php

declare(strict_types=1);

namespace Controllers\Front;

class CategoryController
{
	public function show(string $slug): void
	{
		$slug = trim($slug);
		require __DIR__ . '/../../../templates/front/category-show.php';
	}
}

