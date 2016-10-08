<?php
require_once __DIR__.'/pomodori/router.php';
require_once __DIR__.'/pomodori/api.php';

$router = new pomodori\router();
$router->set_param(array(
    isset($_SERVER['QUERY_STRING'])
        ? rtrim(str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']), '/')
        : rtrim($_SERVER['REQUEST_URI'], '/'),
    $_SERVER['REQUEST_METHOD'],
    $_REQUEST,
    isset($_SERVER['HTTP_ORIGIN'])
        ? isset($_SERVER['HTTP_ORIGIN'])
        : null));
list($route, $param) = $router->parse();

$api = new pomodori\api();
if (!isset($param))
    $api->$route();
else
    $api->$route($param);
?>