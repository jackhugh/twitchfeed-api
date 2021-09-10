<?php

declare(strict_types=1);

use Dotenv\Dotenv;

define("APP_START", microtime(true));

define("__ROOT__", __DIR__);

require_once __ROOT__ . "/vendor/autoload.php";

$dotenv = Dotenv::createImmutable(__ROOT__);
$dotenv->load();

define("__DEV__", $_ENV['ENVIRONMENT'] === 'dev');
