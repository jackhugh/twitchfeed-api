<?php

namespace App\Services;

use App\Mappers\BroadcastersMapper;
use App\Mappers\ClipsMapper;
use App\Mappers\GamesMapper;
use App\Mappers\UpdatesMapper;
use App\Providers\Twitch\AppToken;
use App\Providers\Twitch\TwitchClient;
use App\Providers\TwitchProvider;
use Core\Service;

class UpdateService extends Service
{

	public static function run()
	{

		$client = new TwitchClient($_ENV['TWITCH_CLIENT_ID'], $_ENV['TWITCH_SECRET']);
		$token = new AppToken($client);
		$twitch = new TwitchProvider($token);

		while (1) {

			$broadcasterUpdates = BroadcastersMapper::getUpdates();
			$broadcasterData = $twitch->getBroadcasters($broadcasterUpdates);
			BroadcastersMapper::setBroadcasters($broadcasterData);

			$clipUpdates = UpdatesMapper::getUpdates(100);
			self::logUpdates("clips", $clipUpdates);
			$clipData = $twitch->getClips($clipUpdates);
			ClipsMapper::insertClips($clipData);
			UpdatesMapper::setUpdated($clipUpdates);

			sleep(1);
		}
	}

	private static function logUpdates(string $updateName, array $updates)
	{
		$noOfUpdates = count($updates);
		if ($noOfUpdates > 0) {
			echo "Updating $updateName($noOfUpdates)\n";
		}
	}
}
