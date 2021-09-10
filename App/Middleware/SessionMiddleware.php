<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use App\Providers\SessionProvider;

class SessionMiddleware implements Middleware
{
	public function run(Request $req, Response $resp)
	{
		SessionProvider::create();
	}
}
