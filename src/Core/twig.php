<?php
// src/Core/Twig.php

namespace App\Core;

class Twig
{
    private static $twig = null;

    public static function init()
    {
        if (self::$twig === null) {
            // Démarrer la session si elle n'est pas démarrée
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Chemin vers les templates
            $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
            
            // Configuration de Twig
            self::$twig = new \Twig\Environment($loader, [
                'cache' => false, // Désactive le cache en développement
                'auto_reload' => true,
                'debug' => true,
            ]);
            
            // Ajoutez l'extension de debug
            if (self::$twig->isDebug()) {
                self::$twig->addExtension(new \Twig\Extension\DebugExtension());
            }
            
            // Ajoutez des variables globales SÉCURISÉES
            self::$twig->addGlobal('session', $_SESSION ?? []);
            self::$twig->addGlobal('base_url', '/');
            
            // Ajoutez des filtres personnalisés
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