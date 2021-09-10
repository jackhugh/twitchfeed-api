<?php

namespace App\Mappers;

use App\Models\ClipIndexModel;
use App\Models\ClipModel;
use App\Models\Queries\ClipsSavedQuery;
use App\Models\Queries\SavedQuery;
use App\Models\UserModel;
use App\Providers\QueryBuilder;
use Core\Database\Database;

class SavedMapper
{

	public static function insertSaved(int $userId, SavedQuery $queryData)
	{

		$query = <<<SQL
			INSERT INTO saved (user_id, clip) VALUES (?, (SELECT id FROM clips WHERE slug = ?))
			ON DUPLICATE KEY UPDATE clip = clip
			SQL;

		$db = Database::get();

		$db->query($query, [$userId, $queryData->slug]);
	}

	public static function deleteSaved(int $userId, SavedQuery $queryData)
	{

		$query = <<<SQL
			DELETE FROM saved WHERE user_id = ? AND clip = (SELECT id FROM clips WHERE slug = ?)
			SQL;

		$db = Database::get();
		$db->query($query, [$userId, $queryData->slug]);
	}


	public static function getSavedClips(UserModel $user, ClipsSavedQuery $queryData)
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

			->where("saved.user_id = ?", $user->id)
			->whereIf(!empty($queryData->channel), "clips.broadcaster_id IN (?)", $queryData->channel)
			->whereIf(!empty($queryData->game), "clips.game_id in (?)", $queryData->game)

			->limit(25)
			->offset($queryData->next);

		if ($queryData->sort === "new") {
			$query->orderBy("clips.created_at DESC");
		}
		if ($queryData->sort === "top") {
			$query->orderBy("clips.view_count DESC");
		}
		if ($queryData->sort === "added") {
			$query->orderBy("saved.created_at DESC");
		}

		$data = $query->send();
		return array_map(fn ($elem) => new ClipModel(...$elem), $data);
	}


	public static function getSavedClipsCount(UserModel $user, ClipsSavedQuery $queryData)
	{

		$query = QueryBuilder::SELECT("clips")

			->customField("COUNT(clips.id) AS total")

			->innerJoin("broadcasters", "broadcaster_id", "id")
			->innerJoin("games", "game_id", "id")
			->leftJoin("saved", "id", "clip")

			->where("saved.user_id = ?", $user->id)
			->whereIf(!empty($queryData->channel), "clips.broadcaster_id IN (?)", $queryData->channel)
			->whereIf(!empty($queryData->game), "clips.game_id in (?)", $queryData->game);


		$data = $query->send();

		return (int) $data[0]['total'];
	}

	public static function getSavedClipsIndex(UserModel $user, ClipsSavedQuery $queryData)
	{

		$query = QueryBuilder::SELECT("clips")

			->field("broadcaster_id")
			->field("broadcaster_name")
			->field("game_id")
			->customField("games.game_name")

			->distinct(true)

			->innerJoin("broadcasters", "broadcaster_id", "id")
			->innerJoin("games", "game_id", "id")
			->leftJoin("saved", "id", "clip")

			->where("saved.user_id = ?", $user->id)
			->whereIf(!empty($queryData->channel), "clips.broadcaster_id IN (?)", $queryData->channel)
			->whereIf(!empty($queryData->game), "clips.game_id in (?)", $queryData->game);

		$data = $query->send();

		return array_map(fn ($elem) => new ClipIndexModel(...$elem), $data);
	}
}
