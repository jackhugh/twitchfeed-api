<?php
require_once __DIR__ . "/../../bootstrap.php";

$service = $argv[1] ?? throw new Exception("No service provided.");
$args = [];

for ($i = 2; $i < count($argv); $i++) {
	$args[] = unserialize($argv[$i]);
}

cli_set_process_title($service);

echo "Starting worker $service.\n";
do {
	$val = $service::run(...$args);
	usleep($service::$sleep * 1000000);
} while ($val);
echo "Finished worker $service.\n";
