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
        
        // Nettoyer le chemin
        $path = str_replace("/soso", "", $path);
        
        if ($path === '') {
            $path = '/';
        }

        // Vérifier si la route existe
        if (!isset(self::$routes[$method][$path])) {
            http_response_code(404);
            Twig::display('errors/404.twig');
            return;
        }

        $handler = self::$routes[$method][$path];

        // Si c'est une closure, l'exécuter directement
        if ($handler instanceof \Closure) {
            call_user_func($handler);
            return;
        }

        // Si c'est un tableau [controller, action]
        if (is_array($handler) && count($handler) === 2) {
            [$controllerClass, $action] = $handler;
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }

        // Si c'est une chaîne "Controller@action"
        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controllerClass, $action] = explode('@', $handler);
            
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                
                if (method_exists($controller, $action)) {
                    $controller->$action();
                    return;
                }
            }
        }
        twig::display('errors/500.twig');
    }
}