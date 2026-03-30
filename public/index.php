<?php
/**
 * Front Controller - Point d'entrée unique de l'application
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Autoloader simple - Correction du chemin
spl_autoload_register(function ($class) {
    // Le fichier est dans src/ avec le même namespace
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    } else {
        // Debug : afficher le chemin cherché
        error_log("Fichier non trouvé : " . $file);
    }
});

// Récupérer l'URL - Support pour Apache et Nginx
$url = $_GET['url'] ?? '';
// Si l'URL est vide mais que REQUEST_URI contient quelque chose
if (empty($url) && isset($_SERVER['REQUEST_URI'])) {
    $requestUri = $_SERVER['REQUEST_URI'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $url = trim(str_replace($scriptName, '', $requestUri), '/');
    // Supprimer les paramètres de requête
    $url = strtok($url, '?');
}

$router = new Core\Router();

// Routes du backoffice
$router->get('/admin/login', 'AuthController@showLogin');
$router->post('/admin/login', 'AuthController@login');
$router->get('/admin/logout', 'AuthController@logout');
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

// Route pour la page d'accueil
$router->get('/', 'HomeController@index');

$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($url, $method);

/**
 * Front Controller - Point d'entree unique de l'application
 * Toutes les requetes passent par ce fichier
 */

// declare(strict_types=1);

// error_reporting(E_ALL);
// ini_set('display_errors', '1');

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// spl_autoload_register(static function (string $class): void {
//     $file = __DIR__ . '/../src/' . str_replace('\\', '/', $class) . '.php';

//     if (file_exists($file)) {
//         require $file;
//     }
// });

// require __DIR__ . '/../src/bootstrap.php';

// $router = new Core\Router();

// require __DIR__ . '/../routes/web.php';
// require __DIR__ . '/../routes/admin.php';

// $url = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
// $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// $router->dispatch($url, $method);
