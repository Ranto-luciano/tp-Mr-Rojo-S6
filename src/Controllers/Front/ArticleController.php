<?php

declare(strict_types=1);

namespace Controllers\Front;

class ArticleController
{
	public function show(string $slug): void
	{
		$slug = trim($slug);
		require __DIR__ . '/../../../templates/front/article-show.php';
	}
}

