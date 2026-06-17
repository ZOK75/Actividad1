<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Registro inicial del intento (Tu línea original)
        \Illuminate\Support\Facades\Log::channel('login')->info('[REGISTER_ATTEMPT] Intento de registro de nuevo usuario', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // 2. Cambiamos a Validator::make manual para poder auditar los fallos antes de que Laravel aborte
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ]);

        if ($validator->fails()) {
            //  ESCENARIO A: Falló específicamente el reCAPTCHA
            if ($validator->errors()->has('g-recaptcha-response')) {
                \Illuminate\Support\Facades\Log::channel('login')->alert('[CAPTCHA_FAILED] Intento de registro bloqueado por reCAPTCHA inválido', [
                    'email_intento' => $request->email ?? 'No proporcionado',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            } 
            
            //  ESCENARIO B: Falló por datos erróneos (correo repetido, contraseñas no coinciden, etc.)
            else {
                \Illuminate\Support\Facades\Log::channel('login')->warning('[REGISTER_FAILED] Errores de validación al intentar crear cuenta', [
                    'email_intento' => $request->email,
                    'errores' => $validator->errors()->messages(),
                    'ip' => $request->ip()
                ]);
            }

            // Redirige hacia atrás con los errores y conserva la info vieja
            return back()->withErrors($validator)->withInput();
        }

        // LOG: Registramos que el captcha pasó de forma exitosa
        \Illuminate\Support\Facades\Log::channel('login')->info('[CAPTCHA_PASSED] reCAPTCHA verificado con éxito en Registro', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);


        // 3. TU LÓGICA ORIGINAL INTACTA (Se ejecuta al 100% si la validación pasa)
        $esAdmin = str_ends_with($request->email, '@ive.mx');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $esAdmin,
        ]);

        // ESCENARIO C: Log de Cuenta creada con éxito
        \Illuminate\Support\Facades\Log::channel('login')->info('[REGISTER_SUCCESS] Nueva cuenta creada con éxito', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_admin' => $user->is_admin ? 'Sí' : 'No',
            'ip' => $request->ip()
        ]);

        event(new Registered($user));

        $otp = rand(100000, 999999);
        session()->put('auth_user_otp_code', $otp);

        // Flujo especializado para Administradores de @ive.mx
        if ($user->is_admin) {
            session()->put('2fa_user_id', $user->id);
            session()->save();
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

            return redirect()->route('2fa.register');
        }

        // Flujo para Usuarios normales
        session()->put('auth_pre_user_id', $user->id);
        session()->put('auth_pre_user_email', $user->email);
        session()->save();

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

        return redirect()->route('usuario.otp');
    }
}
