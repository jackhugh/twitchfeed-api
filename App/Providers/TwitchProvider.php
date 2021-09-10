<?php

namespace App\Providers;

use App\Providers\Twitch\Token;
use App\Providers\Twitch\TwitchApi;
use DateTime;
use DateTimeInterface;

class TwitchProvider
{

	protected TwitchApi $api;

	public function __construct(Token $token)
	{
		$this->api = new TwitchApi($token);
	}

	public function getClips(array $requests)
	{

		$twitchRequests = [];

		$times = [
			'TODAY' => (new DateTime())->format(DateTimeInterface::RFC3339),
			'DAY'   => (new DateTime())->modify('-1 day')->format(DateTimeInterface::RFC3339),
			'WEEK'  => (new DateTime())->modify('-1 week')->format(DateTimeInterface::RFC3339),
			'MONTH' => (new DateTime())->modify('-1 month')->format(DateTimeInterface::RFC3339),
			'ALL' => null,
		];

		foreach ($requests as $request) {

			$twitchRequests[] = $this->api->getClips(
				broadcaster_id: $request['broadcaster_id'],
				started_at: $times[$request['update_period']],
				ended_at: $request['update_period'] !== 'ALL' ? $times['TODAY'] : null,
				first: 100,
			);
		}

		$results = $this->api->dispatch($twitchRequests);

		$clips = [];
		foreach ($results as $result) {

			if (!$result->success) continue;

			foreach ($result->body->data as $clip) {

				$clips[] = [
					'slug' => $clip->id,
					'broadcaster_id' => (int) $clip->broadcaster_id,
					'broadcaster_name' => $clip->broadcaster_name,
					'video_id' => (int) $clip->video_id,
					'game_id' => (int) $clip->game_id,
					'title' => $clip->title,
					'view_count' => (int) $clip->view_count,
					'created_at' => (new DateTime($clip->created_at))->format('Y-m-d H:i:s'),
					'thumbnail_url' => $clip->thumbnail_url,
				];
			}
		}

		return $clips;
	}

	public function getFollowing(int $id)
	{
		$api = $this->api;

		$results = $api->dispatch($api->getUsersFollows(first: 100, from_id: $id));

		$following = array_map(fn ($elem) => $elem->to_id, $results->body->data);

		return $following;
	}

	public function getCurrentUser()
	{
		$api = $this->api;

		$user = $api->dispatch($api->getUsers());

		return [
			'id' => $user->body->data[0]->id,
			'displayName' => $user->body->data[0]->display_name,
			'displayPicture' => $user->body->data[0]->profile_image_url
		];
	}

	public function getBroadcasters(array $broadcasters)
	{
		$api = $this->api;

		$idChunks = array_chunk($broadcasters, 100);

		$requests = array_map(fn ($chunkOfIds) => $api->getUsers(id: $chunkOfIds), $idChunks);

		$responses = $api->dispatch($requests);

		$data = [];

		foreach ($responses as $resp) {
			foreach ($resp->body->data as $broadcaster) {

				$data[] = [
					'broadcaster_id' => $broadcaster->id,
					'view_count' => $broadcaster->view_count,
				];
			}
		}

		return $data;
	}

	public function getGames(array $gameIds)
	{
		$api = $this->api;

		$idChunks = array_chunk($gameIds, 100);

		$requests = array_map(fn ($chunkOfIds) => $api->getGames(id: $chunkOfIds), $idChunks);

		$responses = $api->dispatch($requests);

		$data = [];

		foreach ($responses as $resp) {
			foreach ($resp->body->data as $game) {

				$data[] = [
					'game_id' => $game->id,
					'game_name' => $game->name,
				];
			}
		}

		return $data;
	}
}
