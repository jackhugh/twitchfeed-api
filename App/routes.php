<?php

namespace App;

use App\Middleware\AuthenticatedApiMiddleware;
use App\Middleware\SessionMiddleware;
use App\Controllers\ClipsController;
use App\Controllers\SavedClipsController;
use App\Controllers\UserController;
use App\Mappers\UpdatesMapper;
use App\Middleware\CORSMiddleware;
use Core\JSONRoute;
use Core\Router;

Router::addGlobalMiddleware(new CORSMiddleware());
Router::addGlobalMiddleware(new SessionMiddleware());


$auth = new Router();
$auth->addRoute(JSONRoute::GET("/authenticate", UserController::use("authenticate")));
$auth->addRoute(JSONRoute::GET("/logout", UserController::use("logout")));
$auth->addRoute(JSONRoute::GET("/login", UserController::use("login")));

$auth->addRoute(JSONRoute::GET("/status", fn () => UpdatesMapper::getUpcomingUpdates()));

$api = new Router();
$api->addMiddleware(new AuthenticatedApiMiddleware());

$api->addRoute(JSONRoute::GET("/api/clips", ClipsController::use("following")));
$api->addRoute(JSONRoute::GET("/api/clips/index", ClipsController::use("followingIndex")));

$api->addRoute(JSONRoute::GET("/api/saved", SavedClipsController::use("get")));
$api->addRoute(JSONRoute::GET("/api/saved/index", SavedClipsController::use("index")));

$api->addRoute(JSONRoute::POST("/api/saved", SavedClipsController::use("save")));
$api->addRoute(JSONRoute::DELETE("/api/saved", SavedClipsController::use("unsave")));

$api->addRoute(JSONRoute::GET("/api/user", UserController::use("get")));
$api->addRoute(JSONRoute::PUT("/api/user/update", UserController::use("update")));

Router::dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
