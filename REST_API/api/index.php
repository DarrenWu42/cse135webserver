<?php
require "../start.php";

use Src\StaticAPI;
use Src\PerformanceAPI;
use Src\ActivityAPI;
use Src\UserAPI;

$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$routes = ["static","statics","performance","performances","activity","activities","users"];
$get_only_routes = ["statics","performances","activites"];

$static_routes = ["static","statics"];
$perf_routes = ["performance","performances"];
$act_routes = ["activity","activities"];
$user_routes = ["users"];

// endpoints starting with valid routes for GET shows all
// everything else results in a 404 Not Found
if (!in_array($uri[1], $routes)) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// endpoints starting with statics, performances, or activities for POST/PUT/DELETE results in a 404 Not Found
if (in_array($uri[1], $get_only_routes) && isset($uri[2])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$sess_id = isset($uri[2]) ? $uri[2] : null;
$requestMethod = $_SERVER["REQUEST_METHOD"];
$controller;

if(in_array($uri[1], $static_routes))
    $controller = new StaticAPI($dbConnection, $requestMethod, $sess_id);

if(in_array($uri[1], $perf_routes))
    $controller = new PerformanceAPI($dbConnection, $requestMethod, $sess_id);

if(in_array($uri[1], $act_routes))
    $controller = new ActivityAPI($dbConnection, $requestMethod, $sess_id);

if(in_array($uri[1], $user_routes))
    $controller = new UserAPI($dbConnection, $requestMethod, $sess_id);

$controller->processRequest();