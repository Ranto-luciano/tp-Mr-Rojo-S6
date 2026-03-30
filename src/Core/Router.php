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
        // Transforme le pattern de route en expression régulière
        // Exemple: /articles/{id}/edit devient #^/articles/([^/]+)/edit$#
        $pattern = '#^' . preg_replace('/\{([a-z]+)\}/', '([^/]+)', $path) . '$#';
        
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'controllerAction' => $controllerAction,
            'originalPath' => $path
        ];
    }
    
    public function dispatch(string $url, string $method): void
    {
        $url = trim($url, '/');
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }
            
            if (preg_match($route['pattern'], $url, $matches)) {
                // Supprime le premier élément (la correspondance complète)
                array_shift($matches);
                
                // Récupère les noms des paramètres depuis l'URL originale
                preg_match_all('/\{([a-z]+)\}/', $route['originalPath'], $paramNames);
                $paramNames = $paramNames[1];
                
                // Associe les valeurs aux noms des paramètres
                $this->params = array_combine($paramNames, $matches) ?: [];
                
                // Appelle le contrôleur
                $this->callController($route['controllerAction']);
                return;
            }
        }
        
        // Aucune route trouvée -> 404
        $this->notFound();
    }
    
    private function callController(string $controllerAction): void
    {
        list($controllerName, $method) = explode('@', $controllerAction);
        
        $controllerClass = "Controllers\\Back\\{$controllerName}";
        $controllerFile = __DIR__ . "/../Controllers/Back/{$controllerName}.php";
        
        if (!file_exists($controllerFile)) {
            $this->notFound();
            return;
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            $this->notFound();
            return;
        }
        
        call_user_func_array([$controller, $method], $this->params);
    }
    
    private function notFound(): void
    {
        http_response_code(404);
        echo "404 - Page non trouvée";
        exit;
    }
}