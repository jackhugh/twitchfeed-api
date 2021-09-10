<?php

use App\Mappers\BroadcastersMapper;
use App\Mappers\UpdatesMapper;
use App\Providers\Twitch\AppToken;
use App\Providers\Twitch\TwitchApi;
use App\Providers\Twitch\TwitchClient;
use Core\HTTP\Request;

require_once __DIR__ . "/../../bootstrap.php";

$broadcastersPerPage = 50;
$totalBroadcastersNeeded = 1000;
$pagesToScrape = ceil($totalBroadcastersNeeded / $broadcastersPerPage);

$broadcasters = [];

echo "Fetching $pagesToScrape pages\n";

for ($i = 1; $i <= $pagesToScrape; $i++) {

	$url = "https://twitchtracker.com/channels/most-followers?page=$i";

	$req = new Request();
	$req->url($url);
	$req->json(false);
	$resp = $req->send();


	$re = '/<table id="channels" class="table table-condensed text-center.*?<\/table>/ms';
	preg_match_all($re, $resp->body, $table, PREG_SET_ORDER, 0);

	$table = $table[0][0] ?? throw new Exception("Couldn't match table");



	$re = '/<tr>\s*?<td>#.*?<\/td>\s*?<td>\s*?<a.*?<\/a>\s*?<\/td>\s*?<td><a href=".*?">(.*?)<\/a><\/td>/ms';

	preg_match_all($re, $table, $broadcasterMatches, PREG_PATTERN_ORDER, 0);

	$broadcasterMatches = $broadcasterMatches[1] ?? throw new Exception("Couldn't match broadcasters");


	array_push($broadcasters, ...$broadcasterMatches);

	echo "Fetched page $i/$pagesToScrape\n";
	sleep(1);
}

$client = new TwitchClient($_ENV['TWITCH_CLIENT_ID'], $_ENV['TWITCH_SECRET']);
$token = new AppToken($client);
$api = new TwitchApi($token);

$chunks = array_chunk($broadcasters, 100);

$ids = [];

foreach ($chunks as $key => $chunk) {
	$chunkNo = $key + 1;
	echo "Creating request chunk $chunkNo\n";

	$requests = [];

	foreach ($chunk as $broadcaster) {
		$requests[] = $api->getUsers(login: $broadcaster);
	}

	echo "Sending requests to twitch\n";

	$responses = $api->dispatch($requests);

	foreach ($responses as $resp) {
		if (isset($resp->body->data[0]->id)) {
			$ids[] = $resp->body->data[0]->id;
		}
	}

	if ($chunkNo !== count($chunks)) {
		echo "Sleeping...\n";
		sleep(25);
	}
}

$validResponses = count($ids);
echo "Received $validResponses/$totalBroadcastersNeeded valid responses from twitch\n";

echo "Inserting broadcasters into database\n";
BroadcastersMapper::insertBroadcastersIfNull($ids);

echo "Creating updates for broadcasters\n";
UpdatesMapper::insertUpdatesIfNull($ids);
