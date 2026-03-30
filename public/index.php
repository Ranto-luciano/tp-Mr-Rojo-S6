<?php
/**
 * Front Controller - Point d'entrée unique de l'application
 * Toutes les requêtes passent par ce fichier
 */

// Active l'affichage des erreurs en développement
// À désactiver en production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrage de la session
session_start();

// Autoloader simple pour charger les classes automatiquement
spl_autoload_register(function ($class) {
    // Transforme le namespace en chemin de fichier
    // Exemple: Core\Database devient src/Core/Database.php
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Récupère l'URL demandée
// Exemple: /admin/articles/12/edit devient 'admin/articles/12/edit'
$url = $_GET['url'] ?? '';

// Initialise le routeur
$router = new Core\Router();

// Routes du backoffice
// Format: méthode HTTP, URL, Contrôleur@méthode

// Authentification
$router->get('/admin/login', 'AuthController@showLogin');
$router->post('/admin/login', 'AuthController@login');
$router->get('/admin/logout', 'AuthController@logout');

// Dashboard
$router->get('/admin/dashboard', 'DashboardController@index');

// Gestion des articles
$router->get('/admin/articles', 'ArticleAdminController@index');
$router->get('/admin/articles/create', 'ArticleAdminController@create');
$router->post('/admin/articles', 'ArticleAdminController@store');
$router->get('/admin/articles/{id}/edit', 'ArticleAdminController@edit');
$router->post('/admin/articles/{id}', 'ArticleAdminController@update');
$router->post('/admin/articles/{id}/delete', 'ArticleAdminController@delete');
$router->post('/admin/articles/{id}/toggle-publish', 'ArticleAdminController@togglePublish');

// Gestion des catégories
$router->get('/admin/categories', 'CategoryAdminController@index');
$router->get('/admin/categories/create', 'CategoryAdminController@create');
$router->post('/admin/categories', 'CategoryAdminController@store');
$router->get('/admin/categories/{id}/edit', 'CategoryAdminController@edit');
$router->post('/admin/categories/{id}', 'CategoryAdminController@update');
$router->post('/admin/categories/{id}/delete', 'CategoryAdminController@delete');

// Routes pour la gestion des utilisateurs
$router->get('/admin/users', 'UserController@index');
$router->get('/admin/users/create', 'UserController@create');
$router->post('/admin/users', 'UserController@store');
$router->get('/admin/users/{id}/edit', 'UserController@edit');
$router->post('/admin/users/{id}', 'UserController@update');
$router->post('/admin/users/{id}/delete', 'UserController@delete');

// Routes pour le profil utilisateur
$router->get('/admin/users/profile', 'UserController@profile');
$router->post('/admin/users/profile/update', 'UserController@updateProfile');
$router->post('/admin/users/profile/password', 'UserController@changePassword');

// Dispatch la requête
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($url, $method);