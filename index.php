<?php
require_once __DIR__.'/router.php';
require_once __DIR__.'/api.php';

$router = new pomodori\Router();
$router->setUrl($_SERVER["REQUEST_URI"]);
$route = $router->parseUrl();

$api = new pomodori\api();
$api->$route();
?>