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
    // Registrar la actividad entrante antes de que la página cargue
    \Illuminate\Support\Facades\Log::channel('desarrollo')->debug('[WEB_ACTIVITY] Petición entrante HTTP', [
        'metodo' => $request->method(),
        'url' => $request->fullUrl(),
        'usuario_autenticado' => auth()->check() ? auth()->user()->email : 'Invitado',
        'ip' => $request->ip(),
    ]);

    return $next($request);
}
}
