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

        // 2. Registrar la actividad clasificada después de que la respuesta está lista
        $usuario = auth()->check() ? auth()->user()->email : 'Invitado';
        $ip = $request->ip();
        $metodo = $request->method();
        $url = $request->fullUrl();
        $codigoRespuesta = $response->getStatusCode();

        $logData = [
            'metodo' => $metodo,
            'url' => $url,
            'usuario_autenticado' => $usuario,
            'ip' => $ip,
            'codigo_respuesta' => $codigoRespuesta,
        ];

        // Rastrear si hubo redirección
        $esRedireccion = $response->isRedirection();
        if ($esRedireccion) {
            $logData['redireccionado_a'] = $response->headers->get('Location');
        }

        // Rastrear si es una acción de botón/formulario (POST, PUT, PATCH, DELETE)
        $esBotonOFormulario = in_array($metodo, ['POST', 'PUT', 'PATCH', 'DELETE']);
        if ($esBotonOFormulario) {
            // Filtrar datos sensibles para no guardarlos en texto plano
            $datosSensibles = [
                'password', 
                'password_confirmation', 
                'current_password', 
                'new_password', 
                'new_password_confirmation', 
                'token', 
                '_token', 
                'google2fa_secret'
            ];
            $logData['inputs_formulario'] = $request->except($datosSensibles);

            \Illuminate\Support\Facades\Log::channel('desarrollo')->info('[WEB_BUTTON_PRESS] Acción de botón o formulario procesada', $logData);
        } else {
            if ($esRedireccion) {
                \Illuminate\Support\Facades\Log::channel('desarrollo')->info('[WEB_REDIRECT] Redirección de vista', $logData);
            } else {
                \Illuminate\Support\Facades\Log::channel('desarrollo')->debug('[WEB_NAVIGATION] Vista cargada', $logData);
            }
        }

        return $response;
    }
}
