<?php

namespace App\Providers\Twitch;

use Core\HTTP\Request;

abstract class Token
{

	protected TwitchClient $client;

	protected string $token;
	protected int $expiresAt;

	protected int $createdAt;
	protected int $validatedAt;

	protected const VALIDATION_PERIOD = 3600;

	public function __construct(TwitchClient $client)
	{
		$this->client = $client;
		$this->createdAt = time();
		$this->validatedAt = time();
	}

	public function validate()
	{
		if (time() > $this->validatedAt + $this::VALIDATION_PERIOD) {
			$req = new Request();
			$req->url("https://id.twitch.tv/oauth2/validate");
			$req->headers(["Authorization: OAuth {$this->token}"]);
			$resp = $req->send();

			if ($resp->success) {
				$this->validatedAt = time();
			} else {
				throw new TwitchException("Token not valid.");
			}
		}
	}

	public function getToken(): string
	{
		return $this->token;
	}
	public function getClient(): TwitchClient
	{
		return $this->client;
	}
}
