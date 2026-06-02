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
        \Illuminate\Support\Facades\Log::channel('login')->info('[REGISTER_ATTEMPT] Intento de registro de nuevo usuario', [
        'email' => $request->email,
        'ip' => $request->ip(),
        'user_agent' => $request->userAgent()
        ]);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        session()->put('auth_pre_user_id', $user->id);
        session()->put('auth_pre_user_email', $user->email);

        $otp = rand(100000, 999999);
        session()->put('auth_user_otp_code', $otp);

        session()->save();

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

        return redirect()->route('usuario.otp');

        if (!$captchaValido) {
    // 🤖 Alerta de posible BOT
             Log::channel('login')->alert('[CAPTCHA_FAILED] Intento de bypass o captcha incorrecto', [
            'ip' => request()->ip(),
            'formulario' => request()->path(), // Nos dice si fue en login, registro, etc.
            'user_agent' => request()->userAgent()
        ]);
    
         return back()->withErrors(['captcha' => 'Captcha inválido.']);
        }
    }
}
