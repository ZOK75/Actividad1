<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;



class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        Log::channel('login')->info('[GUEST_ACCESS] Invitado visualizando la pantalla de Login', [
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent()
        ]);

        if (!$captchaValido) {
    //  Alerta de posible BOT
            Log::channel('login')->alert('[CAPTCHA_FAILED] Intento de bypass o captcha incorrecto', [
            'ip' => request()->ip(),
            'formulario' => request()->path(), // Nos dice si fue en login, registro, etc.
            'user_agent' => request()->userAgent()
         ]);
    
        return back()->withErrors(['captcha' => 'Captcha inválido.']);
        }

// Opcional: Registrar si pasó con éxito
        Log::channel('login')->info('[CAPTCHA_PASSED] Captcha resuelto correctamente', [
        'ip' => request()->ip()
        ]);
        
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user && $user->is_admin) {
                $adminId = $user->id;
                $adminEmail = $user->email;
                $hasSecret = !empty($user->google2fa_secret);
                Auth::logout();

                $request->session()->put('2fa_user_id', $adminId);

            $emailOtp = rand(100000, 999999);
            $request->session()->put('auth_user_otp_code', $emailOtp);
            $request->session()->save();

            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\OtpMail($emailOtp));

            if (!$hasSecret) {
                return redirect()->route('2fa.register');
            }

            return redirect()->route('2fa.challenge');
        }

        Auth::logout();

        $request->session()->put('auth_pre_user_id', $user->id);
        $request->session()->put('auth_pre_user_email', $user->email);

        $otp = rand(100000, 999999);
        $request->session()->put('auth_user_otp_code', $otp);

        $request->session()->save();

        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

        return redirect()->route('usuario.otp');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
