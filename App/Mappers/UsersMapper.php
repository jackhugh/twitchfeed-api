<?php

namespace App\Mappers;

use Core\Database\Database;

class UsersMapper
{

	public static function getUser(int $id)
	{
		$db = Database::get();

		$user = $db->query("SELECT id from users WHERE id = ?", [$id]);

		return $user[0] ?? null;
	}

	public static function insertUserIfNull(int $id)
	{

		if (empty($id)) return;

		$db = Database::get();

		$db->query("INSERT INTO users (id) VALUES ? ON DUPLICATE KEY UPDATE id = id", [[[$id]]]);
	}
}
