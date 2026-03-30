<?php

declare(strict_types=1);

namespace Core;
use RuntimeException;

class Router
{
    private array $routes = [];
    private array $params = [];
    
    public function get(string $path, string $controllerAction): void
    {
        $this->addRoute('GET', $path, $controllerAction);
    }
    
    public function post(string $path, string $controllerAction): void
    {
        $this->addRoute('POST', $path, $controllerAction);
    }
    
    private function addRoute(string $method, string $path, string $controllerAction): void
    {
        // Normaliser le chemin
        $path = '/' . trim($path, '/');
        
        // Remplacer les paramètres {id} par des regex
        $pattern = '#^' . preg_replace('/\{([a-z]+)\}/', '([^/]+)', $path) . '$#i';
        
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'controllerAction' => $controllerAction,
            'originalPath' => $path
        ];
    }
    
    public function dispatch(string $url, string $method): void
    {
        // Normaliser l'URL
        $url = '/' . trim($url, '/');
        
        // Debug - Afficher les routes pour tester
        error_log("Recherche de la route : " . $url);
        error_log("Méthode : " . $method);
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $url, $matches)) {
                error_log("Route trouvée : " . $route['originalPath']);
                error_log("Pattern : " . $route['pattern']);
                
                array_shift($matches);
                
                preg_match_all('/\{([a-z]+)\}/', $route['originalPath'], $paramNames);
                $paramNames = $paramNames[1];
                
                if (count($paramNames) === count($matches)) {
                    $this->params = array_combine($paramNames, $matches) ?: [];
                } else {
                    $this->params = [];
                }
                
                error_log("Paramètres : " . print_r($this->params, true));
                
                $this->callController($route['controllerAction']);
                return;
            }
        }
        
        error_log("Aucune route trouvée pour : " . $url);
        $this->notFound();
    }
    
    private function callController(string $controllerAction): void
    {
        list($controllerName, $method) = explode('@', $controllerAction);
        
        // Chemin absolu pour les contrôleurs
        $basePath = __DIR__ . '/../Controllers/';
        
        // D'abord, essayer de trouver le contrôleur dans Back
        $backControllerFile = $basePath . "Back/{$controllerName}.php";
        
        if (file_exists($backControllerFile)) {
            require_once $backControllerFile;
            $controllerClass = "Controllers\\Back\\{$controllerName}";
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $this->params);
                    return;
                }
            }
        }
        
        // Ensuite, essayer de trouver le contrôleur dans Front
        $frontControllerFile = $basePath . "Front/{$controllerName}.php";
        
        if (file_exists($frontControllerFile)) {
            require_once $frontControllerFile;
            $controllerClass = "Controllers\\Front\\{$controllerName}";
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $method)) {
                    call_user_func_array([$controller, $method], $this->params);
                    return;
                }
            }
        }
        
        error_log("Contrôleur non trouvé : {$controllerName}@{$method}");
        $this->notFound();
    }
    
    private function notFound(): void
    {
        http_response_code(404);
        echo "404 - Page non trouvée<br>";
        echo "URL demandée: " . htmlspecialchars($_SERVER['REQUEST_URI']);
        exit;
    }

	// private array $routes = [
	// 	'GET' => [],
	// 	'POST' => [],
	// ];

	// public function get(string $path, callable|string $handler): void
	// {
	// 	$this->add('GET', $path, $handler);
	// }

	// public function post(string $path, callable|string $handler): void
	// {
	// 	$this->add('POST', $path, $handler);
	// }

	// public function add(string $method, string $path, callable|string $handler): void
	// {
	// 	$method = strtoupper($method);
	// 	if (!isset($this->routes[$method])) {
	// 		$this->routes[$method] = [];
	// 	}

	// 	$path = $this->normalizePath($path);
	// 	$regex = $this->pathToRegex($path);

	// 	$this->routes[$method][] = [
	// 		'path' => $path,
	// 		'regex' => $regex,
	// 		'handler' => $handler,
	// 	];
	// }

	// public function dispatch(string $uri, string $method): void
	// {
	// 	$method = strtoupper($method);
	// 	$uri = $this->normalizePath($uri);

	// 	foreach ($this->routes[$method] ?? [] as $route) {
	// 		if (preg_match($route['regex'], $uri, $matches) !== 1) {
	// 			continue;
	// 		}

	// 		$params = [];
	// 		foreach ($matches as $key => $value) {
	// 			if (!is_int($key)) {
	// 				$params[$key] = $value;
	// 			}
	// 		}

	// 		$this->execute($route['handler'], $params);
	// 		return;
	// 	}

	// 	$this->renderNotFound($uri);
	// }

	// private function execute(callable|string $handler, array $params): void
	// {
	// 	if (is_callable($handler)) {
	// 		$handler(...array_values($params));
	// 		return;
	// 	}

	// 	if (!str_contains($handler, '@')) {
	// 		throw new RuntimeException('Invalid route handler: ' . $handler);
	// 	}

	// 	[$controllerClass, $action] = explode('@', $handler, 2);

	// 	if (!class_exists($controllerClass)) {
	// 		throw new RuntimeException('Controller class not found: ' . $controllerClass);
	// 	}

	// 	$controller = new $controllerClass();

	// 	if (!method_exists($controller, $action)) {
	// 		throw new RuntimeException('Controller method not found: ' . $handler);
	// 	}

	// 	$controller->{$action}(...array_values($params));
	// }

	// private function normalizePath(string $path): string
	// {
	// 	$path = parse_url($path, PHP_URL_PATH) ?: '/';
	// 	$path = '/' . trim($path, '/');

	// 	return $path === '//' ? '/' : $path;
	// }

	// private function pathToRegex(string $path): string
	// {
	// 	$escaped = preg_quote($path, '#');
	// 	$pattern = preg_replace('#\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}#', '(?P<$1>[^/]+)', $escaped);

	// 	return '#^' . $pattern . '$#';
	// }

	// private function renderNotFound(string $uri): void
	// {
	// 	http_response_code(404);

	// 	if (function_exists('seo_merge') && function_exists('url_for')) {
	// 		$seo = seo_merge([
	// 			'title' => '404 | Page introuvable',
	// 			'description' => 'La page demandee est introuvable.',
	// 			'robots' => 'noindex,follow',
	// 			'canonical' => url_for($uri),
	// 		]);

	// 		ob_start();
	// 		?>
	// 		<section class="hero">
	// 			<h1>404 - Page introuvable</h1>
	// 			<p>Le contenu demande n'existe pas.</p>
	// 			<p><a class="text-link" href="/">Retour a l'accueil</a></p>
	// 		</section>
	// 		<?php
	// 		$content = ob_get_clean();
	// 		require __DIR__ . '/../../templates/layouts/front.php';
	// 		return;
	// 	}

	// 	echo '404 - Page not found';
	// }
}
