<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        // El evento 'Failed' trae los datos intentados en $event->credentials
        Log::channel('login')->warning('Intento de inicio de sesión fallido', [
            'email_intentado' => $event->credentials['email'] ?? 'No provisto',
            'ip'              => request()->ip(),
        ]);
    }
}