<?php

namespace App\Mappers;

use Core\Database\Database;

class BroadcastersMapper
{

	public static function insertBroadcastersIfNull(array $broadcasters)
	{

		if (empty($broadcasters)) return;

		$query = <<<SQL

			INSERT INTO broadcasters (id)
			VALUES ? 
			ON DUPLICATE KEY UPDATE id = id
			SQL;

		$db = Database::get();

		$db->query($query, [array_map(fn ($elem) => [$elem], $broadcasters)]);
	}

	public static function getUpdates()
	{
		$query = <<<SQL

			SELECT id
			FROM broadcasters
			WHERE 1=1
				AND updated_at IS NOT NULL
				AND NOW() > DATE_ADD(updated_at, INTERVAL 7 DAY)

			SQL;

		$db = Database::get();

		$data = $db->query($query);

		return array_map(fn ($elem) => $elem['id'], $data);
	}

	public static function getPriorityUpdates()
	{

		$query = <<<SQL

			SELECT id
			FROM broadcasters
			WHERE updated_at IS NULL

			SQL;

		$db = Database::get();

		$data = $db->query($query);

		return array_map(fn ($elem) => $elem['id'], $data);
	}

	public static function setBroadcasters(array $broadcasterData)
	{

		if (empty($broadcasterData)) return;

		$query = <<<SQL

			INSERT INTO broadcasters (id, view_count)
			VALUES ?
			ON DUPLICATE KEY UPDATE 
				view_count = VALUES(view_count),
				updated_at = NOW()

			SQL;

		$db = Database::get();

		$db->query($query, [$broadcasterData]);
	}
}
