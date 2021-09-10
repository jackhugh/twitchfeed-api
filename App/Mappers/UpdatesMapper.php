<?php

namespace App\Mappers;

use Core\Database\Database;
use Exception;

class UpdatesMapper
{


	public static function insertUpdatesIfNull(array $broadcasters)
	{

		if (empty($broadcasters)) return;

		$db = Database::get();

		$periods = ["ALL", "MONTH", "WEEK", "DAY"];

		$updates = [];

		foreach ($periods as $period) {
			foreach ($broadcasters as $broadcaster) {

				$updates[] = [
					'broadcaster_id' => $broadcaster,
					'update_period' => $period,
				];
			}
		}

		$query = <<<SQL
			INSERT INTO updates (broadcaster_id, update_period) VALUES ?
			ON DUPLICATE KEY UPDATE broadcaster_id = broadcaster_id
			SQL;

		$db->query($query, [$updates]);
	}

	public static function getUpdates(int $amount)
	{

		$query = <<<SQL

			SELECT 
				updates.broadcaster_id, 
				updates.update_period 
			FROM updates
			INNER JOIN periods ON updates.update_period = periods.name
			WHERE 1=1
				AND NOW() > DATE_ADD(updates.updated_at, INTERVAL periods.cache_seconds SECOND)
				AND updates.updated_at IS NOT NULL
			ORDER BY updates.updated_at ASC
			LIMIT $amount

			SQL;

		$db = Database::get();

		return $db->query($query);
	}

	public static function getPriorityUpdates(int $amount)
	{

		$query = <<<SQL

			SELECT 
				updates.broadcaster_id, 
				updates.update_period 
			FROM updates
			WHERE 1=1
				AND updates.updated_at IS NULL
			ORDER BY updates.created_at ASC
			LIMIT $amount

			SQL;

		$db = Database::get();

		return $db->query($query);
	}

	public static function setUpdated(array $updates)
	{

		if (empty($updates)) return;

		$query = <<<SQL

			INSERT INTO updates (broadcaster_id, update_period)
			VALUES ?
			ON DUPLICATE KEY UPDATE updated_at = NOW()

			SQL;

		$db = Database::get();

		$db->query($query, [$updates]);
	}

	public static function getUpdateStatus(array $broadcasterIds)
	{

		$query = <<<SQL

			SELECT !ISNULL(updated_at) AS has_updates
			FROM updates
			WHERE broadcaster_id in (?)

			SQL;

		$db = Database::get();
		$status = $db->query($query, [$broadcasterIds]);

		$numbers = array_map(fn ($elem) => (int) $elem['has_updates'], $status);

		return floor((array_sum($numbers) / count($numbers)) * 100) / 100;;
	}


	public static function getUpcomingUpdates()
	{

		$query = <<<SQL

			SELECT DISTINCT
				DATE_ADD(updates.updated_at, INTERVAL periods.cache_seconds SECOND) AS update_time,
				updates.update_period,
				clips.broadcaster_id,
				clips.broadcaster_name
			FROM updates
			INNER JOIN periods ON updates.update_period = periods.name
			INNER JOIN clips USING(broadcaster_id)
			ORDER BY update_time ASC
			LIMIT 100
			SQL;

		$db = Database::get();

		return $db->query($query);
	}
}
