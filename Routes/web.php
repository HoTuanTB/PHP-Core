<?php


$routeInstance->addRoute('/', [
    'controller' => 'App\Http\Controllers\HomeController',
    'action' => 'index',
]);


$routeInstance->addRoute('/news', [
    'controller' => 'App\Http\Controllers\NewController',
    'action' => 'index',
]);

$routeInstance->addRoute('/product/add', [
    'controller' => 'App\Http\Controllers\HomeController',
    'action' => 'create',
]);