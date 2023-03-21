<?php
require_once 'vendor/autoload.php';

use Src\Controller\ProxyController;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
$requestMethod = $_SERVER["REQUEST_METHOD"];

// return 404 if not proxy request

if ($uri[1] !== 'proxy') {
    header("HTTP/2.0 404 Not Found");
    exit();
}



$action = 0;
if (isset($uri[2])) {

    if($uri[2] === "lock"){
        $action = 1;
    }

    if($uri[2] === "unlock"){
        $action = 2;
    }
    
}

$controller = new ProxyController($requestMethod,$action,"HTTP/2.0");
$controller->processRequest();
