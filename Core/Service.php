<?php

namespace Core;

abstract class Service
{

	public static float $sleep = 0.1;

	// run service in background
	public static function start(...$args)
	{
		foreach ($args as &$arg) {
			$arg = escapeshellarg(serialize($arg));
		}
		$args = implode(" ", $args);
		$location = escapeshellarg(__ROOT__ . "/App/Scripts/start_service.php");
		$class = escapeshellarg(static::class);
		$log = escapeshellarg(__ROOT__ . "/logs/services.log");

		shell_exec("php $location $class $args >> $log 2>&1 &");
	}
}
