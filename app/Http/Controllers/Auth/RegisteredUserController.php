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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        Log::channel('login')->info('[REGISTER_ATTEMPT] Intento de registro de nuevo usuario', [
            'email'      => $request->email,
            'ip'         => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $validator = Validator::make($request->all(), [
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password'             => ['required', 'confirmed', Rules\Password::defaults()],
            'password_confirmation'=> ['required'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'name.required' => 'El campo de nombre esta vacio, por favor completalo',
            'email.required' => 'El campo de email esta vacio, por favor completalo',
            'email.email' => 'Por favor usa un formato de correo valido con @',
            'password.required' => 'El campo de password esta vacio, por favor completalo',
            'password.confirmed' => 'La confirmación del password no coincide.',
            'password.min' => 'El password debe tener al menos 8 caracteres.',
            'password_confirmation.required' => 'El campo de confirmar password esta vacio, por favor completalo',
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.',
            'g-recaptcha-response.recaptcha' => 'El reCAPTCHA no es válido. Inténtalo de nuevo.'
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('g-recaptcha-response')) {
                Log::channel('login')->alert('[CAPTCHA_FAILED] Intento de registro bloqueado por reCAPTCHA inválido', [
                    'email_intento' => $request->email ?? 'No proporcionado',
                    'ip'            => $request->ip(),
                    'user_agent'    => $request->userAgent()
                ]);
            }
            else {
                Log::channel('login')->warning('[REGISTER_FAILED] Errores de validación al intentar crear cuenta', [
                    'email_intento' => $request->email,
                    'errores'       => $validator->errors()->messages(),
                    'ip'            => $request->ip()
                ]);
            }

            return back()->withErrors($validator)->withInput();
        }
        Log::channel('login')->info('[CAPTCHA_PASSED] reCAPTCHA verificado con éxito en Registro', [
            'email' => $request->email,
            'ip'    => $request->ip()
        ]);

        $esAdmin = str_ends_with($request->email, '@ive.mx');

        $datosRegistro = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => $esAdmin,
        ];

        if ($esAdmin) {
            $google2fa = app('pragmarx.google2fa');
            $datosRegistro['google2fa_secret'] = $google2fa->generateSecretKey();
        }

        $request->session()->put('registro_temporal', Crypt::encrypt($datosRegistro));

        $otp = rand(100000, 999999);
        $request->session()->put('registro_otp_code', $otp);
        $request->session()->save();

        \Illuminate\Support\Facades\Mail::to($request->email)->send(new \App\Mail\OtpMail($otp));

        Log::channel('login')->info('[REGISTER_OTP_SENT] OTP de registro enviado, usuario pendiente de verificación (aún no en DB)', [
            'email'    => $request->email,
            'is_admin' => $esAdmin ? 'Sí' : 'No',
            'ip'       => $request->ip()
        ]);

        if ($esAdmin) {
            return redirect()->route('2fa.register');
        }

        return redirect()->route('usuario.otp');
    }
}
