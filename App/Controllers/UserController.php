<?php

namespace App\Controllers;

use App\Mappers\UpdatesMapper;
use App\Models\UserModel;
use App\Providers\SessionProvider;
use App\Providers\Twitch\TwitchClient;
use App\Providers\Twitch\UserToken;
use Core\Controller;
use Core\Exception\HTTPException;
use Exception;

class UserController extends Controller
{

	public function authenticate()
	{
		if (empty($_GET['code'])) throw new HTTPException(400);

		try {
			$client = new TwitchClient($_ENV['TWITCH_CLIENT_ID'], $_ENV['TWITCH_SECRET']);
			$token = new UserToken($client, $_GET['code'], $_ENV['TWITCH_REDIRECT_URI']);
			$_SESSION['user'] = new UserModel($token);

			$redirect = $_SESSION['redirect'] ?? "/";
			unset($_SESSION['redirect']);

			$this->response->redirect($redirect);
		} catch (Exception $e) {
			$this->response->redirect("/?" . http_build_query(['error' => 'error authenticating with twitch']));
		}
	}

	public function logout()
	{
		SessionProvider::destroy();
		$this->response->redirect("/");
	}

	public function login()
	{
		$twitchClient = new TwitchClient($_ENV['TWITCH_CLIENT_ID'], $_ENV['TWITCH_SECRET']);
		$this->response->redirect($twitchClient->generateUrl($_ENV['TWITCH_REDIRECT_URI']));
	}

	public function get()
	{
		$user = UserModel::fromSession();

		return [
			'display_name' => $user->displayName,
			'display_picture' => $user->displayPicture,
			'processed' => UpdatesMapper::getUpdateStatus($user->getFollowing()),
		];
	}

	public function update()
	{
		$user = UserModel::fromSession();
		$user->updateFollowing();
	}
}
