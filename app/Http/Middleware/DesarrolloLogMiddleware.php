<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class DesarrolloLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
    {
        // 1. Dejar que la petición continúe y se procese completamente
        $response = $next($request);

        // 2. Registrar la actividad después de que la respuesta está lista
        \Illuminate\Support\Facades\Log::channel('desarrollo')->debug('[WEB_ACTIVITY] Petición procesada HTTP', [
            'metodo' => $request->method(),
            'url' => $request->fullUrl(),
            'usuario_autenticado' => auth()->check() ? auth()->user()->email : 'Invitado',
            'ip' => $request->ip(),
            'codigo_respuesta' => $response->getStatusCode(),
        ]);

        return $response;
    }
}
