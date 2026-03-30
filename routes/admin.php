<?php

declare(strict_types=1);

$router->get('/admin/login', 'Controllers\\Back\\AuthController@showLogin');
$router->post('/admin/login', 'Controllers\\Back\\AuthController@login');
$router->get('/admin/logout', 'Controllers\\Back\\AuthController@logout');

$router->get('/admin/dashboard', 'Controllers\\Back\\DashboardController@index');

$router->get('/admin/articles', 'Controllers\\Back\\ArticleAdminController@index');
$router->get('/admin/articles/create', 'Controllers\\Back\\ArticleAdminController@create');
$router->post('/admin/articles', 'Controllers\\Back\\ArticleAdminController@store');
$router->get('/admin/articles/{id}/edit', 'Controllers\\Back\\ArticleAdminController@edit');
$router->post('/admin/articles/{id}', 'Controllers\\Back\\ArticleAdminController@update');
$router->post('/admin/articles/{id}/delete', 'Controllers\\Back\\ArticleAdminController@delete');
$router->post('/admin/articles/{id}/toggle-publish', 'Controllers\\Back\\ArticleAdminController@togglePublish');

$router->get('/admin/categories', 'Controllers\\Back\\CategoryAdminController@index');
$router->get('/admin/categories/create', 'Controllers\\Back\\CategoryAdminController@create');
$router->post('/admin/categories', 'Controllers\\Back\\CategoryAdminController@store');
$router->get('/admin/categories/{id}/edit', 'Controllers\\Back\\CategoryAdminController@edit');
$router->post('/admin/categories/{id}', 'Controllers\\Back\\CategoryAdminController@update');
$router->post('/admin/categories/{id}/delete', 'Controllers\\Back\\CategoryAdminController@delete');

$router->get('/admin/users', 'Controllers\\Back\\UserController@index');
$router->get('/admin/users/create', 'Controllers\\Back\\UserController@create');
$router->post('/admin/users', 'Controllers\\Back\\UserController@store');
$router->get('/admin/users/{id}/edit', 'Controllers\\Back\\UserController@edit');
$router->post('/admin/users/{id}', 'Controllers\\Back\\UserController@update');
$router->post('/admin/users/{id}/delete', 'Controllers\\Back\\UserController@delete');

$router->get('/admin/users/profile', 'Controllers\\Back\\UserController@profile');
$router->post('/admin/users/profile/update', 'Controllers\\Back\\UserController@updateProfile');
$router->post('/admin/users/profile/password', 'Controllers\\Back\\UserController@changePassword');
