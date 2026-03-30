<?php

namespace Core;

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
}