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
use Illuminate\Support\Facades\Cache;

class LoginRequest extends FormRequest
{
    public function authenticate(): void
{
    $email = $this->input('email');
    $ip = $this->ip();
    $user = User::where('email', $email)->first();
    $userId = $user ? $user->id : null;

    $this->checkTripleLockout($userId, $email, $ip);

    $this->ensureIsNotRateLimited($userId, $email, $ip);

    if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
        
        
        RateLimiter::hit($this->throttleKey(), 86400); // Se mantiene activo el conteo

        
        if (RateLimiter::attempts($this->throttleKey()) >= 5) {
            $this->applyWaveLockout($user, $email, $ip);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    if ($user) {
        $user->update(['blocked_turns' => 0, 'banned_until' => null]);
    }
    RateLimiter::clear($this->throttleKey());
}


protected function applyWaveLockout($user, $email, $ip)
{
    $turns = $user ? $user->blocked_turns + 1 : 1;
    
    
    if ($turns === 1) {
        $lockoutSeconds = 60; 
        $reason = "Primera oleada superada. Bloqueo de 1 minuto.";
        $level = 'warning';
    } elseif ($turns === 2) {
        $lockoutSeconds = 3600; 
        $reason = "Segunda oleada superada. Bloqueo de 1 hora.";
        $level = 'error';
    } else {
        $lockoutSeconds = 7200; 
        $reason = "Oleada de ataques consecutiva ({$turns}a). Bloqueo de 2 horas.";
        $level = 'critical';
    }

    $bannedUntil = Carbon::now()->addSeconds($lockoutSeconds);

    if ($user) {
        $user->update([
            'blocked_turns' => $turns,
            'banned_until' => $bannedUntil
        ]);
    }

    $userId = $user ? $user->id : 'guest';
    Cache::put("lockout:user:{$userId}", true, $bannedUntil);
    Cache::put("lockout:email:{$email}", true, $bannedUntil);
    Cache::put("lockout:ip:{$ip}", true, $bannedUntil);

    event(new SecurityAlertTriggered($user?->id, $email, $ip, $reason, $level));

    // Limpiar el contador de 5 intentos para que al volver del baneo inicie una nueva oleada limpia
    RateLimiter::clear($this->throttleKey());

    throw ValidationException::withMessages([
        'email' => "Demasiados intentos fallidos. Acceso suspendido temporalmente por seguridad.",
    ]);
}

protected function checkTripleLockout($userId, $email, $ip)
{
    $isBanned = Cache::has("lockout:email:{$email}") || 
                Cache::has("lockout:ip:{$ip}") || 
                ($userId && Cache::has("lockout:user:{$userId}"));

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

public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }

}
