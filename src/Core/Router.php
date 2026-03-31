<?php

declare(strict_types=1);

namespace Core;

class Router
{
	private array $routes = [
		'GET' => [],
		'POST' => [],
	];

	public function get(string $path, callable|string $handler): void
	{
		$this->add('GET', $path, $handler);
	}

	public function post(string $path, callable|string $handler): void
	{
		$this->add('POST', $path, $handler);
	}

	public function add(string $method, string $path, callable|string $handler): void
	{
		$method = strtoupper($method);
		if (!isset($this->routes[$method])) {
			$this->routes[$method] = [];
		}

		$path = $this->normalizePath($path);
		$regex = $this->pathToRegex($path);

		$this->routes[$method][] = [
			'path' => $path,
			'regex' => $regex,
			'handler' => $handler,
		];
	}

	public function dispatch(string $uri, string $method): void
	{
		$method = strtoupper($method);
		$uri = $this->normalizePath($uri);

		foreach ($this->routes[$method] ?? [] as $route) {
			if (preg_match($route['regex'], $uri, $matches) !== 1) {
				continue;
			}

			$params = [];
			foreach ($matches as $key => $value) {
				if (!is_int($key)) {
					$params[$key] = $value;
				}
			}

			$this->execute($route['handler'], $params);
			return;
		}

		$this->renderNotFound($uri);
	}

	private function execute(callable|string $handler, array $params): void
	{
		if (is_callable($handler)) {
			$handler(...array_values($params));
			return;
		}

		if (!str_contains($handler, '@')) {
			throw new \RuntimeException('Invalid route handler: ' . $handler);
		}

		[$controllerClass, $action] = explode('@', $handler, 2);
		$controllerClass = $this->resolveControllerClass($controllerClass);

		if (!class_exists($controllerClass)) {
			throw new \RuntimeException('Controller class not found: ' . $controllerClass);
		}

		$controller = new $controllerClass();

		if (!method_exists($controller, $action)) {
			throw new \RuntimeException('Controller method not found: ' . $handler);
		}

		$args = $this->coerceArguments($controller, $action, array_values($params));
		$controller->{$action}(...$args);
	}

	private function coerceArguments(object $controller, string $action, array $args): array
	{
		$method = new \ReflectionMethod($controller, $action);
		$parameters = $method->getParameters();
		$coerced = [];

		foreach ($parameters as $index => $parameter) {
			$value = $args[$index] ?? null;
			$type = $parameter->getType();

			if (!$type instanceof \ReflectionNamedType || $type->isBuiltin() === false || $value === null) {
				$coerced[] = $value;
				continue;
			}

			switch ($type->getName()) {
				case 'int':
					$coerced[] = (int) $value;
					break;
				case 'float':
					$coerced[] = (float) $value;
					break;
				case 'bool':
					$coerced[] = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;
					break;
				case 'string':
					$coerced[] = (string) $value;
					break;
				default:
					$coerced[] = $value;
			}
		}

		return $coerced;
	}

	private function resolveControllerClass(string $controllerClass): string
	{
		if (str_contains($controllerClass, '\\')) {
			return $controllerClass;
		}

		$front = 'Controllers\\Front\\' . $controllerClass;
		if (class_exists($front)) {
			return $front;
		}

		$back = 'Controllers\\Back\\' . $controllerClass;
		if (class_exists($back)) {
			return $back;
		}

		return $controllerClass;
	}

	private function normalizePath(string $path): string
	{
		$path = parse_url($path, PHP_URL_PATH) ?: '/';
		$path = '/' . trim($path, '/');

		return $path === '//' ? '/' : $path;
	}

	private function pathToRegex(string $path): string
	{
		$escaped = preg_quote($path, '#');
		$pattern = preg_replace('#\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}#', '(?P<$1>[^/]+)', $escaped);

		return '#^' . $pattern . '$#';
	}

	private function renderNotFound(string $uri): void
	{
		http_response_code(404);

		if (function_exists('seo_merge') && function_exists('url_for')) {
			$seo = seo_merge([
				'title' => '404 | Page introuvable',
				'description' => 'La page demandee est introuvable.',
				'robots' => 'noindex,follow',
				'canonical' => url_for($uri),
			]);

			ob_start();
			?>
			<section class="hero">
				<h1>404 - Page introuvable</h1>
				<p>Le contenu demande n'existe pas.</p>
				<p><a class="text-link" href="/">Retour a l'accueil</a></p>
			</section>
			<?php
			$content = ob_get_clean();
			require __DIR__ . '/../../templates/layouts/front.php';
			return;
		}

		echo '404 - Page not found';
	}
}
