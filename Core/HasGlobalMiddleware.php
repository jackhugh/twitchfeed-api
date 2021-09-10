<?php

namespace Core;

trait HasGlobalMiddleware
{

	protected static array $staticMiddleware = [];

	public static function addGlobalMiddleware(Middleware $middleware)
	{
		static::$staticMiddleware[] = $middleware;
	}

	public static function runStaticMiddleware(Request $request, Response $response)
	{
		foreach (static::$staticMiddleware as $middleware) {
			$middleware->run($request, $response);
		}
	}
}
