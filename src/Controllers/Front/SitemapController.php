<?php

declare(strict_types=1);

namespace Controllers\Front;

class SitemapController
{
    public function index(): void
    {
        require __DIR__ . '/../../../public/sitemap.xml.php';
    }
}
