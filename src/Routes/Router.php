<?php

namespace App\Routes;
use App\Core\Twig;
class Router
{
    public static $routes = [];

    private static function addRoute($route, $handler, $method)
    {
        self::$routes[$method][$route] = $handler;
    }

    public static function get($route, $handler)
    {
        self::addRoute($route, $handler, "GET");
    }

    public static function post($route, $handler)
    {
        self::addRoute($route, $handler, "POST");
    }

    public static function put($route, $handler)
    {
        self::addRoute($route, $handler, "PUT");
    }

    public static function delete($route, $handler)
    {
        self::addRoute($route, $handler, "DELETE");
    }

    public static function dispatch()
{
    $path = strtok($_SERVER['REQUEST_URI'], "?");
    $method = $_SERVER['REQUEST_METHOD'];

    $path = str_replace("/soso", "", $path);
    if ($path === '') {
        $path = '/';
    }

    if (!isset(self::$routes[$method])) {
        http_response_code(404);
        Twig::display('errors/404.twig');
        return;
    }

    foreach (self::$routes[$method] as $route => $handler) {

        // Convert /admin/users/{id} → regex
        $pattern = preg_replace('#\{[a-zA-Z_]+\}#', '([0-9]+)', $route);
        $pattern = "#^" . $pattern . "$#";

        if (preg_match($pattern, $path, $matches)) {

            array_shift($matches); // remove full match → keep params

            // Closure
            if ($handler instanceof \Closure) {
                call_user_func_array($handler, $matches);
                return;
            }

            // [Controller::class, 'method']
            if (is_array($handler) && count($handler) === 2) {
                [$controllerClass, $action] = $handler;
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $matches);
                return;
            }

            // "Controller@method"
            if (is_string($handler) && strpos($handler, '@') !== false) {
                [$controllerClass, $action] = explode('@', $handler);
                $controller = new $controllerClass();
                call_user_func_array([$controller, $action], $matches);
                return;
            }
        }
    }

    http_response_code(500);
    Twig::display('errors/500.twig');
}

}