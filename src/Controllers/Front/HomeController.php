<?php

declare(strict_types=1);

namespace Controllers\Front;

class HomeController
{
	public function index(): void
	{
		require __DIR__ . '/../../../templates/front/home.php';
	}
}

