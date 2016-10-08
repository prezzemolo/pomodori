<?php
require_once __DIR__.'/pomodori/router.php';
require_once __DIR__.'/pomodori/api.php';

$router = new pomodori\router();
$router->set_param(array(
    'url' => isset($_SERVER['QUERY_STRING'])
        ? str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'])
        : $_SERVER['REQUEST_URI'],
    'method' => $_SERVER['REQUEST_METHOD'],
    'param' => $_REQUEST,
    'cors' => isset($_SERVER['HTTP_Origin'])
        ? isset($_SERVER['HTTP_Origin'])
        : null));
list($route, $param) = $router->parse();

$api = new pomodori\api();
if (!isset($param))
    $api->$route();
else
    $api->$route($param);
?><?php
require_once __DIR__.'/pomodori/router.php';
require_once __DIR__.'/pomodori/api.php';

$router = new pomodori\router();
$router->set_param(array(
    'url' => isset($_SERVER['QUERY_STRING'])
        ? str_replace('?'.$_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'])
        : $_SERVER['REQUEST_URI'],
    'method' => $_SERVER['REQUEST_METHOD'],
    'param' => $_REQUEST));
list($route, $param) = $router->parse();

$api = new pomodori\api();
if (!isset($param))
    $api->$route();
else
    $api->$route($param);
?>