<?php

declare(strict_types=1);

namespace Controllers\Back;

class DashboardController
{
	public function index(): void
	{
		require __DIR__ . '/../../../templates/back/dashboard.php';
	}
}

