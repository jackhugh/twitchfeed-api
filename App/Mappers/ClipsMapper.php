<?php

namespace App\Mappers;

use App\Models\ClipIndexModel;
use App\Models\ClipModel;
use App\Models\Queries\ClipsFollowingQuery;
use App\Models\Queries\ClipsIndexQuery;
use App\Models\UserModel;
use App\Providers\QueryBuilder;
use Core\Database\Database;

class ClipsMapper
{

	public static function insertClips(array $clips)
	{

		if (empty($clips)) return;

		$query = <<<SQL
			
			INSERT INTO clips (slug, broadcaster_id, broadcaster_name, video_id, game_id, title, view_count, created_at, thumbnail_url)
			VALUES ?
			ON DUPLICATE KEY UPDATE view_count = VALUES(view_count), updated = NOW()

			SQL;


		$db = Database::get();

		$db->query($query, [$clips]);
	}

	public static function getClips(UserModel $user, ClipsFollowingQuery $queryData)
	{

		$query = QueryBuilder::SELECT("clips")

			->field("title")
			->field("slug")
			->field("view_count")
			->customField("UNIX_TIMESTAMP(clips.created_at) AS created_at")
			->field("thumbnail_url")
			->field("broadcaster_id")
			->field("broadcaster_name")
			->field("game_id")
			->customField("games.game_name")
			->customField("!ISNULL(saved.id) AS saved")

			->innerJoin("broadcasters", "broadcaster_id", "id")
			->innerJoin("games", "game_id", "id")
			->leftJoin("saved", "id", "clip")

			->where("clips.broadcaster_id IN (?)", $queryData->channel)
			->whereIf(!empty($queryData->game), "clips.game_id in (?)", $queryData->game)

			->limit(25)
			->offset($queryData->next);

		if ($queryData->period !== "ALL") {
			$query->where(QueryBuilder::maxAge("clips.created_at", "1 {$queryData->period}"));
		}

		$query->orderBy(match ($queryData->sort) {
			'hot' => '(POWER(clips.view_count, 4) / broadcasters.view_count) / POWER((NOW() - clips.created_at), 3) DESC',
			'new' => 'clips.created_at DESC',
			'top' => 'clips.view_count DESC',
		});

		$data = $query->send();
		return array_map(fn ($elem) => new ClipModel(...$elem), $data);
	}

	public static function getClipsCount(ClipsFollowingQuery $queryData)
	{

		$query = QueryBuilder::SELECT("clips")

			->customField("COUNT(clips.id) AS total")

			->innerJoin("broadcasters", "broadcaster_id", "id")
			->innerJoin("games", "game_id", "id")

			->where("clips.broadcaster_id IN (?)", $queryData->channel)
			->whereIf(!empty($queryData->game), "clips.game_id in (?)", $queryData->game);

		if ($queryData->period !== "ALL") {
			$query->where(QueryBuilder::maxAge("clips.created_at", "1 {$queryData->period}"));
		}

		$data = $query->send();

		return (int) $data[0]['total'];
	}

	public static function getClipsIndex(UserModel $user, ClipsIndexQuery $queryData)
	{

		$query = QueryBuilder::SELECT("clips")

			->field("broadcaster_id")
			->field("broadcaster_name")
			->field("game_id")
			->customField("games.game_name")

			->distinct(true)

			->innerJoin("broadcasters", "broadcaster_id", "id")
			->innerJoin("games", "game_id", "id")

			->where("clips.broadcaster_id IN (?)", $user->getFollowing());

		if ($queryData->period !== "ALL") {
			$query->where(QueryBuilder::maxAge("clips.created_at", "1 {$queryData->period}"));
		}

		$data = $query->send();

		return array_map(fn ($elem) => new ClipIndexModel(...$elem), $data);
	}
}
