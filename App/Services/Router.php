<?php

namespace App\Services;


class Router
{
    private $routes;

    public function __construct()
    {
        $this->routes = getRoutesList();
    }

    public function run()
    {
        $uri = trim($_SERVER['REQUEST_URI'], '/');
        $method = strtolower($_SERVER["REQUEST_METHOD"]);
        if (array_key_exists($method, $this->routes)) {
            $routes = $this->routes[$method];
            $result = $this->getController($uri, $routes);
        }
        if (empty($result)) {
            header('Content-type:application/json');
            http_response_code(404);
            echo json_encode(["messages" => ['Page not found']]);
            die;
        }
    }

    /**
     * @param string $uri
     * @param array $routes
     * @return bool
     */
    private function getController(string $uri, array $routes)
    {
        foreach ($routes as $uriPattern => $path) {
            if (preg_match('~^' . $uriPattern . '$~', $uri)) {
                $segments = explode('@', $path);
                $controllerName = array_shift($segments);
                $actionName = array_shift($segments);
                $controllerObject = new $controllerName;
                if (method_exists($controllerObject, 'checkPermissions')) {
                    if (!$controllerObject->checkPermissions($actionName)) {
                        echo 'Not enough rights for this';
                        die;
                    }
                }
                $result = $controllerObject->$actionName();

                if (!empty($result)) {
                    return $result;
                }
            }
        }
        return false;
    }
}
