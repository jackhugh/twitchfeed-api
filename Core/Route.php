<?php

namespace Core;

use Closure;
use ReflectionMethod;

class Route
{

	use HasMiddleware;
	use RouteFactories;

	public string $regex;
	public array $params = [];


	public function __construct(
		public string $verb,
		public string $route,
		public Closure|ReflectionMethod $controller,
		public string $type = "HTML",
	) {
		$regex = $this->route;

		// Escape route for regex.
		$regex = preg_quote($regex, "/");

		// Find all route parameters.
		$regex = preg_replace_callback("/\\\{(.+?)\\\}/", function ($matches) {

			// Store the param name for recalling later.
			$this->params[] = $matches[1];

			// Replace with regex for matching request route.
			// Match any character that is not a slash at least once (ungreedy)
			return "([^\/]+?)";
		}, $regex);

		// Enclose final regex ready for use.
		$this->regex = "/^$regex$/";
	}

	public function match(Request $request): bool
	{
		if (
			$this->verb === $request->method &&
			preg_match_all($this->regex, $request->url, $matches, PREG_SET_ORDER)
		) {
			$this->setParams($matches, $request);
			return true;
		} else {
			return false;
		}
	}

	public function setParams(array $matches, Request $request)
	{
		$matches = $matches[0];
		array_shift($matches);
		$params = [];
		foreach ($this->params as $key => $param) {
			$params[$param] = $matches[$key];
		}
		$request->params = (object) $params;
	}

	public function run(Request $req, Response $resp)
	{
		if (is_callable($this->controller)) {
			$function = $this->controller;
			return $function($req, $resp);
		} else {
			$controllerMethod = $this->controller->name;
			$controllerClass = $this->controller->class;

			$controller = new $controllerClass($req, $resp);
			return $controller->$controllerMethod();
		}
	}
}
