<?php

namespace App\Providers\Twitch;

use Core\HTTP\Request;

class AppToken extends Token
{

	public function __construct(TwitchClient $client)
	{

		parent::__construct(...func_get_args());

		$req = new Request();
		$req->method("POST");
		$req->url("https://id.twitch.tv/oauth2/token?" . http_build_query([
			'grant_type' => 'client_credentials',
			'client_id' => $this->client->id,
			'client_secret' => $this->client->secret,
		]));
		$resp = $req->send();

		if ($resp->success) {
			$this->token = $resp->body->access_token;
			$this->expiresAt = $resp->body->expires_in + time();
		} else {
			throw new TwitchException("Could not create token.");
		}
	}
}
