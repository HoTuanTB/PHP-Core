<?php
require __DIR__ . '/../autoload/autoload.php';
$routeInstance = new \Routes\Route();
require __DIR__ . '/../Routes/web.php';
$routeInstance->dispatchRoute($_SERVER['REQUEST_URI']);