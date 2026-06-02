<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\SecurityAlertTriggered;
use Illuminate\Support\Facades\Log;

class LogSecurityAlert
{
    public function handle(SecurityAlertTriggered $event): void
    {
        $message = "ALERTA DE SEGURIDAD INTERNA: {$event->reason}";
        
        // Estructura de datos requerida
        $context = [
            'solicitante_id' => $event->userId ?? 'N/A (Usuario no registrado)',
            'email_objetivo' => $event->email,
            'ip_origen'      => $event->ip,
        ];

        // Clasificación por criticidad
        if ($event->level === 'critical') {
            Log::channel('login')->critical($message, $context);
        } elseif ($event->level === 'error') {
            Log::channel('login')->error($message, $context);
        } else {
            Log::channel('login')->warning($message, $context);
        }
    }
}