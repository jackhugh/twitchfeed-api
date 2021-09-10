<?php

namespace App\Providers\Twitch;

use Core\HTTP\MultiRequest;
use Core\HTTP\Request;
use Exception;

class TwitchApiCore
{

	protected Token $token;

	protected array $requests = [];

	public function __construct(Token $token)
	{
		$this->token = $token;
	}

	public function dispatch(TwitchRequest|array $requests)
	{

		$arrayPassed = is_array($requests);
		$requests = $arrayPassed ? $requests : [$requests];

		$multiReq = $this->buildMultiRequest($requests);

		try {

			$results = $this->sendMultiRequest($multiReq);
			return $arrayPassed ? $results : $results[0];
		} catch (Exception $e) {

			$this->token->refreshToken();
			$results = $this->sendMultiRequest($multiReq);

			return $arrayPassed ? $results : $results[0];
		}
	}

	protected function sendMultiRequest(MultiRequest $multiReq)
	{

		$responses = $multiReq->send();

		$responseData = [];

		foreach ($responses as $resp) {
			if ($resp->status === 401) {
				throw new Exception();
			}

			$responseData[] = $resp;
			// $responseData[] = $resp->body;
		}

		return $responseData;
	}

	protected function buildMultiRequest(array $requests): MultiRequest
	{

		$multi = new MultiRequest();

		foreach ($requests as $requestData) {
			$clientId = $this->token->getClient()->id;
			$bearer = $this->token->getToken();

			$req = new Request();
			$req->method($requestData->method);
			$req->url($requestData->endpoint . "?" . http_build_query($requestData->params));
			$req->headers(["Client-ID: {$clientId}", "Authorization: Bearer {$bearer}"]);

			$multi->addRequest($req);
		}

		return $multi;
	}
}
