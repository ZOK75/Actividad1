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
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'g-recaptcha-response' => ['required', 'recaptcha'],
        ], [
            'email' => ['required', 'string', 'email'],
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.',
            'g-recaptcha-response.recaptcha' => 'El reCAPTCHA no es válido. Inténtalo de nuevo.'
        ]);
    
        if ($validator->fails()) {
            \Illuminate\Support\Facades\Log::channel('login')->alert('[CAPTCHA_FAILED] Intento de inicio de sesión bloqueado por reCAPTCHA inválido', [
                'email_intento' => $request->email ?? 'No proporcionado',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()->withErrors($validator)->withInput();
        }

        \Illuminate\Support\Facades\Log::channel('login')->info('[CAPTCHA_PASSED] reCAPTCHA verificado con éxito en Login', [
            'email' => $request->email,
            'ip' => $request->ip()
        ]);

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

            \Illuminate\Support\Facades\Log::channel('login')->info('[OTP_GENERATED] Código OTP enviado a Administrador para 2FA', [
                'email_solicitado' => $adminEmail,
                'ip' => $request->ip()
            ]);

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

        \Illuminate\Support\Facades\Log::channel('login')->info('[OTP_GENERATED] Código OTP enviado a Usuario para Segundo Factor', [
            'email_solicitado' => $user->email,
            'ip' => $request->ip()
        ]);

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
