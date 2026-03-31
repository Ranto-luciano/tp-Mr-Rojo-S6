<?php

declare(strict_types=1);

use Models\Article;
use Models\Category;

function article_model(): Article
{
    static $model = null;

    if (!$model instanceof Article) {
        $model = new Article();
    }

    return $model;
}

function category_model(): Category
{
    static $model = null;

    if (!$model instanceof Category) {
        $model = new Category();
    }

    return $model;
}

function article_latest(int $limit = 9): array
{
    return article_model()->article_latest($limit);
}

function article_find_by_slug(string $slug): ?array
{
    return article_model()->article_find_by_slug($slug);
}

function article_by_category_slug(string $categorySlug, int $limit = 20): array
{
    return article_model()->article_by_category_slug($categorySlug, $limit);
}

function article_search(string $query, int $limit = 20): array
{
    return article_model()->article_search($query, $limit);
}

function article_related(string $categorySlug, string $excludeSlug, int $limit = 3): array
{
    return article_model()->article_related($categorySlug, $excludeSlug, $limit);
}

function article_sitemap_items(): array
{
    return article_model()->article_sitemap_items();
}

function category_all_with_counts(): array
{
    return category_model()->category_all_with_counts();
}

function category_find_by_slug(string $slug): ?array
{
    return category_model()->category_find_by_slug($slug);
}
