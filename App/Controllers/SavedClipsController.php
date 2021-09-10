<?php

namespace App\Controllers;

use App\Mappers\SavedMapper;
use App\Models\Queries\ClipsSavedQuery;
use App\Models\Queries\SavedQuery;
use App\Models\UserModel;
use Core\Controller;
use Core\Exception\HTTPException;
use Throwable;

class SavedClipsController extends Controller
{

	public function save()
	{

		try {
			$query = new SavedQuery($_GET);
		} catch (Throwable $t) {
			throw new HTTPException(400);
		}

		$user = UserModel::fromSession();

		try {
			SavedMapper::insertSaved($user->id, $query);
			return true;
		} catch (Throwable $t) {
			throw new HTTPException(404);
		}
	}

	public function unsave()
	{

		try {
			$query = new SavedQuery($_GET);
		} catch (Throwable $t) {
			throw new HTTPException(400);
		}

		$user = UserModel::fromSession();

		SavedMapper::deleteSaved($user->id, $query);
		return true;
	}

	public function get()
	{

		try {
			$query = new ClipsSavedQuery(...$_GET);
		} catch (Throwable $t) {
			throw new HTTPException(400);
		}

		$user = UserModel::fromSession();

		return [
			'total' => SavedMapper::getSavedClipsCount($user, $query),
			'data' => SavedMapper::getSavedClips($user, $query),
		];
	}

	public function index()
	{

		try {
			$query = new ClipsSavedQuery(...$_GET);
		} catch (Throwable $t) {
			throw new HTTPException(400);
		}

		$user = UserModel::fromSession();

		return [
			'data' => SavedMapper::getSavedClipsIndex($user, $query),
		];
	}
}
