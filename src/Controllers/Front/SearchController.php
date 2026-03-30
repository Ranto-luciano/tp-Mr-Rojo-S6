<?php

declare(strict_types=1);

namespace Controllers\Front;

class SearchController
{
	public function index(): void
	{
		require __DIR__ . '/../../../templates/front/search.php';
	}
}

