<?php

namespace App\Middleware;

use Core\Middleware;

class CORSMiddleware implements Middleware
{

	public function run($req, $resp)
	{

		$resp->headers['Access-Control-Allow-Origin'] = "http://localhost:3000";
		$resp->headers['Access-Control-Allow-Credentials'] = "true";
		$resp->headers['Access-Control-Allow-Methods'] = "POST, GET, OPTIONS, PUT, DELETE";

		if ($req->method === "OPTIONS") {
			$resp->responseCode = 204;
			$resp->send();
		}
	}
}
