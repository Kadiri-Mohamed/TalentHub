<?php
// src/Core/Twig.php

namespace App\Core;

class Twig
{
    private static $twig = null;

    public static function init()
    {
        if (self::$twig === null) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
            
            self::$twig = new \Twig\Environment($loader, [
                'cache' => false, // Désactive le cache en développement
                'auto_reload' => true,
                'debug' => true,
            ]);
            
            if (self::$twig->isDebug()) {
                self::$twig->addExtension(new \Twig\Extension\DebugExtension());
            }
            
            self::$twig->addGlobal('session', $_SESSION ?? []);
            self::$twig->addGlobal('base_url', '/');
            
            self::$twig->addFilter(new \Twig\TwigFilter('capitalize', function ($string) {
                return ucwords(strtolower($string));
            }));
        }
        
        return self::$twig;
    }
    
    public static function render(string $template, array $data = []): string
    {
        return self::init()->render($template, $data);
    }
    
    public static function display(string $template, array $data = []): void
    {
        echo self::render($template, $data);
    }
}