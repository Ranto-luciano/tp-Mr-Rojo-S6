<?php

declare(strict_types=1);

$router->get('/', 'Controllers\\Front\\HomeController@index');

$router->get('/search', 'Controllers\\Front\\SearchController@index');
$router->get('/recherche.html', 'Controllers\\Front\\SearchController@index');

$router->get('/article/{slug}', 'Controllers\\Front\\ArticleController@show');
$router->get('/category/{slug}', 'Controllers\\Front\\CategoryController@show');

$router->get('/actualites/article-{slug}.html', 'Controllers\\Front\\ArticleController@show');
$router->get('/rubriques/{slug}.html', 'Controllers\\Front\\CategoryController@show');

$router->get('/sitemap.xml', 'Controllers\\Front\\SitemapController@index');
