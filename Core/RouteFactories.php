<?php

namespace Core;

use Closure;
use ReflectionMethod;

// Route factories
trait RouteFactories
{
	public static function GET(string $route, Closure|ReflectionMethod $controller, string $type = "HTML")
	{
		return new static(__FUNCTION__, ...func_get_args());
	}
	public static function POST(string $route, Closure|ReflectionMethod $controller, string $type = "HTML")
	{
		return new static(__FUNCTION__, ...func_get_args());
	}
	public static function PUT(string $route, Closure|ReflectionMethod $controller, string $type = "HTML")
	{
		return new static(__FUNCTION__, ...func_get_args());
	}
	public static function PATCH(string $route, Closure|ReflectionMethod $controller, string $type = "HTML")
	{
		return new static(__FUNCTION__, ...func_get_args());
	}
	public static function DELETE(string $route, Closure|ReflectionMethod $controller, string $type = "HTML")
	{
		return new static(__FUNCTION__, ...func_get_args());
	}
	public static function OPTIONS(string $route, Closure|ReflectionMethod $controller, string $type = "HTML")
	{
		return new static(__FUNCTION__, ...func_get_args());
	}
}
