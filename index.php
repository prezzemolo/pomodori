<?php
require_once __DIR__.'/router.php';
require_once __DIR__.'/api.php';

$router = new pomodori\router();
$router->setParam(array(
    "url" => isset($_SERVER['QUERY_STRING'])
        ? str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'])
        : $_SERVER['REQUEST_URI'],
    "method" => $_SERVER['REQUEST_METHOD'],
    "" => $_REQUEST));
list($route, $param) = $router->parseUrl();

$api = new pomodori\api();
if (!isset($param))
    $api->$route();
else
    $api->$route($param);
?>