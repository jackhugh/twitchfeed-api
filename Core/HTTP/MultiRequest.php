<?php

namespace Core\HTTP;

class MultiRequest
{

	private $mh;
	private $requests = [];

	public function __construct()
	{
		$this->mh = curl_multi_init();
	}
	public function addRequest(Request $request): MultiRequest
	{

		curl_multi_add_handle($this->mh, $request->getCurl());
		$this->requests[] = $request;

		return $this;
	}

	public function send(): array
	{

		$running = null;
		do {
			curl_multi_exec($this->mh, $running);
			curl_multi_select($this->mh);
		} while ($running);

		$responses = [];

		foreach ($this->requests as $request) {
			$response = (object) [
				'success' => null,
				'status' => null,
				'message' => null,
				'headers' => 'Not enabled',
				'body' => null
			];
			$ch = $request->getCurl();

			$responseString = curl_multi_getcontent($ch);
			$info = curl_getinfo($ch);
			$error = curl_error($ch);

			$response->success = ($info['http_code'] >= 200 and $info['http_code'] < 400) ? true : false;
			$response->status = $info['http_code'];

			if (!empty($error)) {
				$response->message = $error;
			} elseif ($response->success == false) {
				$response->message = 'Error';
			} else {
				$response->message = 'Success';
			}

			if (!empty($request->getHeaders())) $response->headers = $request->getHeaders();
			if ($responseString != false) $response->body = json_decode($responseString);

			$responses[] = $response;
		}
		return $responses;
	}
}
