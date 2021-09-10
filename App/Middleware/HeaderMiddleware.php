<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;

class HeaderMiddleware implements Middleware
{

	protected array $headers;

	public function __construct(array $headers)
	{
		$this->headers = $headers;
	}

	public function run(Request $req, Response $resp)
	{
		$resp->headers = array_merge($resp->headers, $this->headers);
	}
}
