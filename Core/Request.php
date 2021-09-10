<?php

namespace Core;

use stdClass;

class Request
{

	public stdClass $params;
	public stdClass $query;
	public stdClass $body;
	public stdClass $cookies;

	public function __construct(
		public string $method,
		public string $url
	) {
		$this->method = $method;
		$this->url = $url;
		$this->params = (object) [];
		$this->query = (object) $_GET;
		$this->post = (object) $_POST;
		$this->cookies = (object) $_COOKIE;
	}
}
