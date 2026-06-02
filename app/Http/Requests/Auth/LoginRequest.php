<?php

namespace App\Http\Requests\Auth;

use App\Events\SecurityAlertTriggered;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoginRequest extends FormRequest
{
    public function authenticate(): void
{
    $email = $this->input('email');
    $ip = $this->ip();
    $user = User::where('email', $email)->first();
    $userId = $user ? $user->id : null;

    // 1. VALIDACIÓN TRIPLE DE BANEO (Por ID, Correo o IP en caché)
    $this->checkTripleLockout($userId, $email, $ip);

    // 2. VALIDACIÓN DEL RATE LIMITER (Los 5 intentos de la oleada actual)
    $this->ensureIsNotRateLimited($userId, $email, $ip);

    // 3. INTENTO DE LOGIN NORMAL
    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        
        // Sumar intento fallido a la oleada actual
        RateLimiter::hit($this->throttleKey(), 86400); // Se mantiene activo el conteo

        // Si con este fallo se llega a los 5 intentos -> Aplicamos la oleada de baneo
        if (RateLimiter::attempts($this->throttleKey()) >= 5) {
            $this->applyWaveLockout($user, $email, $ip);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    // Si el inicio de sesión es exitoso, reiniciamos el contador de intentos y oleadas
    if ($user) {
        $user->update(['blocked_turns' => 0, 'banned_until' => null]);
    }
    RateLimiter::clear($this->throttleKey());
}

/**
 * Aplica el baneo progresivo según la oleada actual
 */
protected function applyWaveLockout($user, $email, $ip)
{
    $turns = $user ? $user->blocked_turns + 1 : 1;
    
    // Determinar el tiempo de baneo según la oleada (FÁCILMENTE EDITABLE AQUÍ)
    if ($turns === 1) {
        $lockoutSeconds = 60; // 1a Oleada: 1 minuto (Para pruebas)
        $reason = "Primera oleada superada. Bloqueo de 1 minuto.";
        $level = 'warning';
    } elseif ($turns === 2) {
        $lockoutSeconds = 3600; // 2a Oleada: 1 hora (Para pruebas / producción)
        $reason = "Segunda oleada superada. Bloqueo de 1 hora.";
        $level = 'error';
    } else {
        $lockoutSeconds = 7200; // 3a Oleada en adelante: 2 horas
        $reason = "Oleada de ataques consecutiva ({$turns}a). Bloqueo de 2 horas.";
        $level = 'critical';
    }

    $bannedUntil = Carbon::now()->addSeconds($lockoutSeconds);

    // Guardar en Base de Datos si el usuario existe
    if ($user) {
        $user->update([
            'blocked_turns' => $turns,
            'banned_until' => $bannedUntil
        ]);
    }

    // GUARDAR EN CACHÉ EL BLOQUEO TRIPLE (Para asegurar IP y correos inexistentes)
    $userId = $user ? $user->id : 'guest';
    Cache::put("lockout:user:{$userId}", true, $bannedUntil);
    Cache::put("lockout:email:{$email}", true, $bannedUntil);
    Cache::put("lockout:ip:{$ip}", true, $bannedUntil);

    // Disparar alerta de seguridad para los logs
    event(new SecurityAlertTriggered($user?->id, $email, $ip, $reason, $level));

    // Limpiar el contador de 5 intentos para que al volver del baneo inicie una nueva oleada limpia
    RateLimiter::clear($this->throttleKey());

    throw ValidationException::withMessages([
        'email' => "Demasiados intentos fallidos. Acceso suspendido temporalmente por seguridad.",
    ]);
}

/**
 * Verifica si alguno de los 3 aspectos está bajo un baneo activo
 */
protected function checkTripleLockout($userId, $email, $ip)
{
    $isBanned = Cache::has("lockout:email:{$email}") || 
                Cache::has("lockout:ip:{$ip}") || 
                ($userId && Cache::has("lockout:user:{$userId}"));

    // Adicionalmente comprobar directo en Base de Datos por seguridad redundante del usuario
    if (!$isBanned && $userId) {
        $user = User::find($userId);
        if ($user && $user->banned_until && Carbon::now()->lt($user->banned_until)) {
            $isBanned = true;
        }
    }

    if ($isBanned) {
        throw ValidationException::withMessages([
            'email' => "Esta cuenta, IP o solicitud se encuentra temporalmente bloqueada debido a políticas de seguridad corporativas.",
        ]);
    }
}

protected function ensureIsNotRateLimited($userId, $email, $ip): void
{
    if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
        return;
    }

    throw ValidationException::withMessages([
        'email' => 'Has alcanzado el límite de intentos de esta oleada.',
    ]);
}

}
