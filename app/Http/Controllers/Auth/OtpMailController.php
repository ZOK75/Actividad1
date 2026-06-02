<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OtpMailController extends Controller
{
    public function processInvitadoCheckpoint(Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => ['required', 'recaptcha'],
            'invitado_email' => ['required', 'email']
        ], [
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.',
            'invitado_email.required' => 'El correo electrónico es obligatorio.',
            'invitado_email.email' => 'Por favor, ingresa un correo electrónico válido.'
        ]);

        $otp = rand(100000, 999999);

        session()->put('invitado_otp_code', $otp);
        session()->put('invitado_otp_email', $request->invitado_email);
        
        Mail::to($request->invitado_email)->send(new OtpMail($otp));

        // 📝 LOG: Registro de generación de OTP para invitado
        Log::channel('login')->info('[OTP_GENERATED] Se ha solicitado un código OTP para Invitado', [
            'email_solicitado' => $request->invitado_email,
            'ip' => $request->ip(),
            'dispositivo' => $request->userAgent()
        ]);

        return redirect()->route('invitado.otp');
    }

    public function showInvitadoOtpForm()
    {
        if (!session()->has('invitado_otp_code')) {
            return redirect()->route('invitado.checkpoint');
        }
        return view('auth.invitado-otp');
    }

    public function verifyInvitadoOtp(Request $request)
    {
        $request->validate(['otp_input' => 'required|digits:6']);

        $emailInvitado = session()->get('invitado_otp_email');

        if ($request->otp_input == session()->get('invitado_otp_code')) {
            session()->put('invitado_verificado', true);
            
            // 📝 LOG: Verificación exitosa
            Log::channel('login')->info('[OTP_VERIFIED] Invitado verificó su OTP con éxito', [
                'email' => $emailInvitado,
                'ip' => $request->ip()
            ]);

            session()->forget(['invitado_otp_code', 'invitado_otp_email']);
            
            return redirect()->route('dashboard');
        }

        // 📝 LOG: Intento fallido de código OTP
        Log::channel('login')->warning('[OTP_FAILED] Código OTP incorrecto insertado por invitado', [
            'email_intento' => $emailInvitado,
            'codigo_ingresado' => $request->otp_input,
            'ip' => $request->ip()
        ]);

        return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
    }

    public function showUsuarioOtpForm()
    {
        if (!session()->has('auth_user_otp_code')) {
            return redirect()->route('login');
        }
        return view('auth.usuario-otp');
    }

    public function verifyUsuarioOtp(Request $request)
    {
        $request->validate(['otp_input' => 'required|digits:6']);

        $emailUsuario = session()->get('auth_pre_user_email');

        if ($request->otp_input == session()->get('auth_user_otp_code')) {
            
            $user = User::find(session()->get('auth_pre_user_id'));

            if ($user) {
                Auth::login($user);

                // 📝 LOG: Verificación de segundo factor exitosa para usuario registrado
                Log::channel('login')->info('[2FA_VERIFIED] Usuario autenticado completamente mediante OTP', [
                    'email' => $user->email,
                    'ip' => $request->ip()
                ]);

                session()->forget(['auth_user_otp_code', 'auth_pre_user_id', 'auth_pre_user_email']);
                
                session()->save();

                return redirect()->route('dashboard');
            }
        }

        // 📝 LOG: Intento de OTP fallido para usuario registrado
        Log::channel('login')->warning('[2FA_FAILED] Código OTP de segundo factor incorrecto', [
            'email_usuario' => $emailUsuario,
            'codigo_ingresado' => $request->otp_input,
            'ip' => $request->ip()
        ]);

        return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
    }
}