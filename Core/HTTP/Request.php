<?php

namespace Core\HTTP;

class Request
{
	private $ch;
	private bool $json = true;
	private $headers = [];

	public static function buildUrl(string $url, array $params = [], ...$args)
	{
		$paramsString = http_build_query($params, ...$args);
		return "{$url}?{$paramsString}";
	}

	public function __construct($url = null)
	{
		$this->ch = curl_init();

		// Output as string
		curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

		// Follow redirects (max 5)
		curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);


		if (!empty($url)) {
			$this->url($url);
		}
	}

	// Set Request URL
	public function url(string $url): Request
	{

		if (filter_var($url, FILTER_VALIDATE_URL)) {
			curl_setopt($this->ch, CURLOPT_URL, $url);
			return $this;
		} else {
			throw new \Exception("Invalid URL");
		}
	}

	public function json(bool $enabled)
	{
		$this->json = $enabled;
	}

	// Set method, default GET
	public function method(string $method): Request
	{
		curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
		return $this;
	}

	public function postParams()
	{
	}

	// Set headers
	public function headers(array $headers): Request
	{
		curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
		return $this;
	}

	public function getHeaders(): array
	{
		return $this->headers;
	}

	// Return curl resource
	public function getCurl()
	{
		return $this->ch;
	}

	// Set option with curl_setopt function
	public function setOpt($option, $value): Request
	{
		curl_setopt($this->ch, $option, $value);
		return $this;
	}

	public function returnHeaders($enable)
	{
		$headerFunc = function ($curl, $header) {
			$len = strlen($header);
			$header = explode(':', $header, 2);
			if (count($header) < 2) // ignore invalid headers
				return $len;

			$this->headers[strtolower(trim($header[0]))][] = trim($header[1]);

			return $len;
		};
		curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, $enable ? $headerFunc : function () {
		});

		return $this;
	}

	// Send Request and handle response
	public function send()
	{

		$response = (object) [
			'success' => null,
			'status' => null,
			'message' => null,
			'headers' => 'Not enabled',
			'body' => null
		];


		$responseString = curl_exec($this->ch);
		$info = curl_getinfo($this->ch);
		$error = curl_error($this->ch);

		$response->success = ($info['http_code'] >= 200 and $info['http_code'] < 400) ? true : false;
		$response->status = $info['http_code'];

		if (!empty($error)) {
			$response->message = $error;
		} elseif ($response->success == false) {
			$response->message = 'Error';
		} else {
			$response->message = 'Success';
		}

		if (!empty($this->headers)) $response->headers = $this->headers;
		if ($responseString != false) {
			$response->body = $this->json ? json_decode($responseString) : $responseString;
		}

		return $response;
	}
}
