<?php

namespace App\Providers;

class SessionProvider
{

	public static function create()
	{
		// cookie params
		session_set_cookie_params([
			// cookie can be accessed from all paths
			'path' => '/',
			// cookie can be accessed this domain only
			'domain' => preg_replace("/.*www/", "", $_SERVER['SERVER_NAME']),
			// only over https connections
			'secure' => true,
			// javascript cannot access cookie but still sent with ajax
			'httponly' => true,
			// only send cookies in a first party context
			'samesite' => 'Strict',
			// set cookie expires
			//TEMP
			'lifetime' => 86400 * 365,
		]);

		// ini params.
		session_start([
			'name' => 'id',
			// REVIEW
			'gc_probability' => 0,
			'save_path' => __ROOT__ . "/sess"
		]);
	}

	public static function destroy()
	{
		// reset session global for this script instance
		$_SESSION = [];

		// delete cookie
		$params = session_get_cookie_params();
		setcookie(
			session_name(),
			session_id(),
			[
				'expires' => 1,
				'path' => $params['path'],
				'domain' => $params['domain'],
				'secure' => $params['secure'],
				'httponly' => $params['httponly'],
				'samesite' => $params['samesite'],
			]
		);

		// destroy session data on server
		session_destroy();
	}
}
