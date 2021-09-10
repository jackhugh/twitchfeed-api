<?php

namespace Core;

use PDO;

abstract class Mapper
{

	private static $staticDB;
	protected $DB;

	private function getDB()
	{
		$server = $_ENV['DB_HOST'];
		$database = $_ENV['DB_NAME'];
		$username = $_ENV['DB_USER'];
		$password = $_ENV['DB_PASSWORD'];

		static::$staticDB ??= new PDO("mysql:host=$server;dbname=$database", $username, $password);
		return static::$staticDB;
	}

	public final function __construct()
	{
		$this->DB = $this->getDB();
	}
}
