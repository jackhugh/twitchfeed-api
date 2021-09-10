<?php

namespace App\Providers\Twitch;

class TwitchClient
{

	public string $id;
	public string $secret;

	public function __construct(string $clientId, string $secret)
	{
		$this->id = $clientId;
		$this->secret = $secret;
	}

	public function generateUrl(string $redirectUri, array $scopes = [], bool $forceVerify = null, $state = null)
	{
		$url = "https://id.twitch.tv/oauth2/authorize?";
		$url .= http_build_query([
			'client_id' => $this->id,
			'redirect_uri' => $redirectUri,
			'response_type' => 'code',
			'scope' => !empty($scopes) ? implode(" ", $scopes) : null,
			'force_verify' => $forceVerify,
			'state' => $state,
		]);
		return $url;
	}
}
