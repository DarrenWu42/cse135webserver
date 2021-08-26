<?php
require "../start.php";
use Src\Post;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$routes = ["static","statics","performance","performances","activity","activities"];
$get_only_routes = ["statics","performances","activites"];

// endpoints starting with valid routes for GET shows all
// everything else results in a 404 Not Found
if (!in_array($uri[1], $routes)) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

// endpoints starting with statics, performances, or activities for POST/PUT/DELETE results in a 404 Not Found
if (in_array($uri[1],$get_only_routes) && isset($uri[2])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}

$postId = isset($uri[2]) ? $uri[2] : null;
$requestMethod = $_SERVER["REQUEST_METHOD"];

// pass the request method and post ID to the Post and process the HTTP request:
$controller = new Post($dbConnection, $requestMethod, $postId);
$controller->processRequest();