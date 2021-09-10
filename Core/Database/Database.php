<?php

namespace Core\Database;

class Database
{

	protected static PDOPlus $instance;

	public static function get()
	{
		self::$instance ??= new PDOPlus($_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
		return self::$instance;
	}
}
