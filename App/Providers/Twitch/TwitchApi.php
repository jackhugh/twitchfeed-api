<?php

namespace App\Providers\Twitch;

class TwitchApi extends TwitchApiCore
{

	public function getTopGames(
		string $after = null,
		string $before = null,
		int $first = null,
	) {
		return new TwitchRequest("https://api.twitch.tv/helix/games/top", get_defined_vars());
	}

	public function getClips(
		int $broadcaster_id = null,
		int $game_id = null,
		int $id = null,
		int $first = null,
		string $started_at = null,
		string $ended_at = null,
		string $after = null,
		string $before = null,
	) {
		return new TwitchRequest("https://api.twitch.tv/helix/clips", get_defined_vars());
	}

	public function getUsersFollows(
		string $after = null,
		int $first = null,
		int $from_id = null,
		int $to_id = null,
	) {
		return new TwitchRequest("https://api.twitch.tv/helix/users/follows", get_defined_vars());
	}

	public function getUsers(
		int|array $id = null,
		string|array $login = null,
	) {
		return new TwitchRequest("https://api.twitch.tv/helix/users", get_defined_vars());
	}

	public function getGames(
		int|array $id = null,
		string $name = null
	) {
		return new TwitchRequest("https://api.twitch.tv/helix/games", get_defined_vars());
	}
}
