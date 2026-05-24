<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventBackHistory
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Solo aplicar a usuarios autenticados
        if (auth()->check()) {
            // Headers más estrictos para prevenir caché
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('Surrogate-Control', 'no-store');
            
            // Prevenir que el navegador guarde la página en el historial
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }
        
        return $response;
    }
}