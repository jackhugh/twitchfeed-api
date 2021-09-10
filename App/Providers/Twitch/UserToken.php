<?php

namespace App\Providers\Twitch;

use Core\HTTP\Request;

class UserToken extends Token
{

	protected string $refreshToken;

	public function __construct(TwitchClient $client, string $code, string $redirectUri)
	{

		parent::__construct(...func_get_args());

		$req = new Request();
		$req->method("POST");
		$req->url("https://id.twitch.tv/oauth2/token?" . http_build_query([
			'grant_type' => 'authorization_code',
			'client_id' => $this->client->id,
			'client_secret' => $this->client->secret,
			'code' => $code,
			'redirect_uri' => $redirectUri,
		]));
		$resp = $req->send();

		if ($resp->success) {
			$this->token = $resp->body->access_token;
			$this->expiresAt = $resp->body->expires_in + time();
			$this->refreshToken = $resp->body->refresh_token;
		} else {
			throw new TwitchException("Could not create token.");
		}
	}

	public function refreshToken()
	{

		$req = new Request();
		$req->method("POST");
		$req->url("https://id.twitch.tv/oauth2/token?" . http_build_query([
			'grant_type' => 'refresh_token',
			'client_id' => $this->client->id,
			'client_secret' => $this->client->secret,
			'refresh_token' => $this->refreshToken,
		]));
		$resp = $req->send();

		if ($resp->success) {
			$this->token = $resp->body->access_token;
			$this->bearerExpiry = $resp->body->expires_in + time();
			$this->refreshToken = $resp->body->refresh_token;
			$this->validatedAt = time();
		} else {
			throw new TwitchException("Could not refresh token.");
		}
	}
}
