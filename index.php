<?php
require_once __DIR__.'/router.php';
require_once __DIR__.'/api.php';

$router = new Router();
$router->setUrl($_SERVER["REQUEST_URI"]);
$route = $router->parseUrl();

$api = new api();
$api->$route();
?>