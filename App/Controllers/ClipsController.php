<?php

namespace App\Controllers;

use App\Mappers\ClipsMapper;
use App\Models\Queries\ClipsFollowingQuery;
use App\Models\Queries\ClipsIndexQuery;
use App\Models\UserModel;
use Core\Controller;
use Core\Exception\HTTPException;
use Throwable;

class ClipsController extends Controller
{

	public function following()
	{

		try {
			$query = new ClipsFollowingQuery(...$_GET);
		} catch (Throwable $t) {
			throw new HTTPException(400);
		}

		$user = UserModel::fromSession();

		if (empty($query->channel)) $query->channel = $user->getFollowing();

		return [
			'total' => ClipsMapper::getClipsCount($query),
			'data' => ClipsMapper::getClips($user, $query),
		];
	}

	public function followingIndex()
	{

		try {
			$query = new ClipsIndexQuery(...$_GET);
		} catch (Throwable $t) {
			throw new HTTPException(400);
		}

		$user = UserModel::fromSession();

		if (empty($query->channel)) $query->channel = $user->getFollowing();

		return [
			'data' => ClipsMapper::getClipsIndex($user, $query),
		];
	}
}
