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
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Mensajes personalizados para la validación
     */
    public function messages(): array
    {
        return [
            'email.email' => 'Por favor usa un formato de correo valido con @',
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.',
            'g-recaptcha-response.recaptcha' => 'El reCAPTCHA no es válido. Inténtalo de nuevo.'
        ];
    }

    public function authenticate(): void
    {
        $email = $this->input('email');
        $ip = $this->ip();
        $user = User::where('email', $email)->first();
        $userId = $user ? $user->id : null;

        // 1. Revisar si ya está baneado por el triple bloqueo corporativo
        $this->checkTripleLockout($userId, $email, $ip);

        // 2. Revisar si ya agotó los intentos de la oleada actual
        $this->ensureIsNotRateLimited($userId, $email, $ip);

        // 3. Intento de autenticación nativo
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            
            //LOG: Intento fallido de Login (Contraseña incorrecta o correo inexistente)
            \Illuminate\Support\Facades\Log::channel('login')->warning('[LOGIN_FAILED] Credenciales erróneas introducidas en el Login', [
                'email_intento' => $email,
                'ip' => $ip,
                'user_agent' => $this->userAgent()
            ]);

            RateLimiter::hit($this->throttleKey(), 86400); // Se mantiene activo el conteo

            if (RateLimiter::attempts($this->throttleKey()) >= 5) {
                $this->applyWaveLockout($user, $email, $ip);
            }

            throw ValidationException::withMessages([
                'email' => 'Alguna de tus credenciales ingresadas son incorrectas, intentalo de nuevo',
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

        // LOG: Registrar baneo activo en tu canal de auditoría
        \Illuminate\Support\Facades\Log::channel('login')->critical("[BAN_ACTIVATED] {$reason}", [
            'email' => $email,
            'ip' => $ip,
            'oleada_turno' => $turns,
            'banned_until' => $bannedUntil->toDateTimeString()
        ]);

        event(new SecurityAlertTriggered($user?->id, $email, $ip, $reason, $level));

        RateLimiter::clear($this->throttleKey());

        // Mensaje dinámico para la pantalla de Login que indica el tiempo
        $tiempoTexto = $lockoutSeconds >= 3600 ? ($lockoutSeconds / 3600) . ' hora(s)' : '1 minuto';
        throw ValidationException::withMessages([
            'email' => "Demasiados intentos fallidos. Acceso suspendido por {$tiempoTexto} debido a políticas corporativas.",
        ]);
    }

    protected function checkTripleLockout($userId, $email, $ip)
    {
        $isBanned = Cache::has("lockout:email:{$email}") || 
                    Cache::has("lockout:ip:{$ip}") || 
                    ($userId && Cache::has("lockout:user:{$userId}"));

        $bannedUntilTime = null;

        if ($userId) {
            $user = User::find($userId);
            if ($user && $user->banned_until) {
                $bannedUntilTime = Carbon::parse($user->banned_until);
                if (Carbon::now()->lt($bannedUntilTime)) {
                    $isBanned = true;
                }
            }
        }

        if ($isBanned) {
            // Calcular dinámicamente los minutos o horas restantes para mostrar en la vista
            if ($bannedUntilTime) {
                $diffInSeconds = Carbon::now()->diffInSeconds($bannedUntilTime, false);
                if ($diffInSeconds > 0) {
                    if ($diffInSeconds >= 3600) {
                        $restante = ceil($diffInSeconds / 3600) . ' hora(s)';
                    } else {
                        $restante = ceil($diffInSeconds / 60) . ' minuto(s)';
                    }
                    $mensajeError = "Esta solicitud se encuentra bloqueada. Intenta de nuevo en {$restante} por seguridad corporativa.";
                } else {
                    $mensajeError = "Esta cuenta, IP o solicitud se encuentra temporalmente bloqueada debido a políticas de seguridad corporativas.";
                }
            } else {
                $mensajeError = "Acceso restringido temporalmente por bloqueo de seguridad perimetral.";
            }

            // LOG: Registrar que un usuario bloqueado intentó ingresar antes de tiempo
            \Illuminate\Support\Facades\Log::channel('login')->notice('[BANNED_USER_ATTEMPT] Intento de acceso denegado a un objetivo actualmente bloqueado', [
                'email' => $email,
                'ip' => $ip
            ]);

            throw ValidationException::withMessages([
                'email' => $mensajeError,
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

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 1. Si los campos básicos (email/password) están mal formados, registramos y abortamos
            if ($validator->errors()->any()) {
                \Illuminate\Support\Facades\Log::channel('login')->warning('[VALIDATION_ERROR] Campos mal formados en el Login', [
                    'ip' => $this->ip(),
                    'errores' => $validator->errors()->messages(),
                    'user_agent' => $this->userAgent()
                ]);
                return; // Frenamos aquí
            }

            // 2. VALIDACIÓN MANUAL DEL reCAPTCHA
            // Evaluamos el campo directamente del request para que no choque con las reglas de Laravel
            $captchaInput = $this->input('g-recaptcha-response');

            // Modifica esta condición si tu paquete de captcha usa otra forma de verificar (ej: Validator::execute..)
            if (!$captchaInput) { 
                // LOG: El usuario no marcó el captcha
                \Illuminate\Support\Facades\Log::channel('login')->alert('[CAPTCHA_FAILED] Intento de inicio de sesión bloqueado por reCAPTCHA vacío o inválido', [
                    'email_intento' => $this->input('email'),
                    'ip' => $this->ip(),
                    'user_agent' => $this->userAgent()
                ]);

                // Agregamos el error manualmente al validador
                $validator->errors()->add('g-recaptcha-response', 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.');
            } else {
                // LOG: Si el campo del captcha trae datos, asumimos éxito para la bitácora
                \Illuminate\Support\Facades\Log::channel('login')->info('[CAPTCHA_PASSED] reCAPTCHA enviado con éxito en Login', [
                    'email' => $this->input('email'),
                    'ip' => $this->ip()
                ]);
            }
        });
    }
      
}