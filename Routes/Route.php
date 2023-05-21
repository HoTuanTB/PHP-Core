<?php

namespace Routes;
use App\Http\Controllers\HomeController;

class Route
{
    private $routes = [];

    public function addRoute($path, $routeInfo)
    {
        $this->routes[$path] = $routeInfo;
    }

    public function dispatchRoute($pathCurrent)
    {
        $pathCurrent = explode("?", $pathCurrent);
        $linkUrl = $pathCurrent[0];
        $IdCondition = $pathCurrent[1];
        preg_match('/(\d+)/', $IdCondition, $matches);
        $id = $matches[0];
        foreach ($this->routes as $path => $routeCurrent) {
            if ($path == $linkUrl) {
                $controller = $routeCurrent['controller'];
                $action = $routeCurrent['action'];
                $instanceController = new  $controller;
                $instanceController->id = $id;
                $instanceController->$action($id);
            }
        }
    }
}