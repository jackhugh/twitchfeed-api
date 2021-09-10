<?php

namespace App\Providers\Twitch;

class TwitchRequest
{

	public function __construct(
		public string $endpoint,
		public array $params = [],
		public string $method = "GET",
	) {
	}
}
