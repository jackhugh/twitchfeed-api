<?php

namespace App\Middleware;

use Core\Exception\HTTPException;
use Core\Middleware;
use Core\Request;
use Core\Response;
use Exception;

class AuthenticatedApiMiddleware implements Middleware
{

	public function run(Request $req, Response $resp)
	{

		if (!isset($_SESSION['user'])) {
			throw new HTTPException(401);
		}

		$user = $_SESSION['user'];

		try {
			$user->token->validate();
		} catch (Exception $e) {
			try {
				$user->token->refreshToken();
				$user->token->validate();
			} catch (Exception $e) {
				throw new HTTPException(401);
			}
		}
	}
}
