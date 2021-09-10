<?php

namespace App\Models;

use App\Mappers\BroadcastersMapper;
use App\Mappers\UpdatesMapper;
use App\Mappers\UsersMapper;
use App\Providers\Twitch\Token;
use App\Providers\TwitchProvider;

class UserModel
{

	public $token;

	public int $id;
	public string $displayName;
	public string $displayPicture;

	protected array $following;
	protected ?int $followingUpdatedAt = null;

	protected const FOLLOWING_REFRESH_PERIOD = 3600;

	public static function fromSession(): self
	{
		return $_SESSION['user'] ?? null;
	}

	public function __construct(Token $token)
	{

		$this->token = $token;

		$this->updateTwitchInfo();
		$this->updateFollowing();

		UsersMapper::insertUserIfNull($this->id);
	}

	public function updateTwitchInfo()
	{

		$twitch = new TwitchProvider($this->token);

		$userInfo = $twitch->getCurrentUser();
		$this->id = $userInfo['id'];
		$this->displayName = $userInfo['displayName'];
		$this->displayPicture = $userInfo['displayPicture'];
	}

	public function updateFollowing()
	{

		$twitch = new TwitchProvider($this->token);
		$following = $twitch->getFollowing($this->id);

		$this->following = $following;
		$this->followingUpdatedAt = time();

		BroadcastersMapper::insertBroadcastersIfNull($this->following);

		UpdatesMapper::insertUpdatesIfNull($this->following);
	}

	public function getFollowing()
	{
		if (time() > $this->followingUpdatedAt + $this::FOLLOWING_REFRESH_PERIOD) {
			$this->updateFollowing();
		}
		return $this->following;
	}
}
