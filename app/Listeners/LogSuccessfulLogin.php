<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        // El evento 'Login' ya trae los datos del usuario de forma nativa en $event->user
        Log::channel('login')->info('Inicio de sesión exitoso', [
            'usuario_id' => $event->user->id,
            'email'      => $event->user->email,
            'ip'         => request()->ip(),
        ]);
    }
}