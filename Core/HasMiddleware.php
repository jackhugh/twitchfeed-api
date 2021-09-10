<?php

namespace Core;

trait HasMiddleware
{

	protected array $middleware = [];

	public function addMiddleware(Middleware $middleware)
	{
		$this->middleware[] = $middleware;
		return $this;
	}

	public function runMiddleware(Request $request, Response $response)
	{
		foreach ($this->middleware as $middleware) {
			$middleware->run($request, $response);
		}
	}
}
