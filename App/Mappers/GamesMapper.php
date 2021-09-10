<?php

namespace App\Mappers;

use Core\Database\Database;

class GamesMapper
{

	public static function insertGames(array $games)
	{

		if (empty($games)) return;

		$query = <<<SQL

			INSERT INTO games (id, game_name) VALUES ?
				ON DUPLICATE KEY UPDATE id=id

			SQL;

		$db = Database::get();

		$db->query($query, [$games]);
	}

	public static function getGamesUpdates()
	{

		$query = <<<SQL

			SELECT DISTINCT id FROM games
			WHERE 1=1
				AND game_name IS NULL
				AND id != 0
		
			SQL;

		$db = Database::get();

		$data = $db->query($query);

		return array_map(fn ($elem) => $elem['id'], $data);
	}
}
