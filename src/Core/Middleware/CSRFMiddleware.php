<?php

namespace App\Core\Middleware;

use App\Core\Session;

class CSRFMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        Session::init();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            
            if (!$token || !hash_equals(Session::get('csrf_token'), $token)) {
                http_response_code(419);
                echo "Token CSRF invalide ou expiré.";
                exit;
            }
        }
    }
}