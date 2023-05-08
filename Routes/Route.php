<?php

namespace Routes;

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
        $pathCurrent = $pathCurrent[0];
        foreach ($this->routes as $path => $routeCurrent) {
            if ($path == $pathCurrent) {
                $controller = $routeCurrent['controller'];
                $action = $routeCurrent['action'];
                $instanceController = new  $controller;
                $instanceController->$action();
            }
        }
    }
}