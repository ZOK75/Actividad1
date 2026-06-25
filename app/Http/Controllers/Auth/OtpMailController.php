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
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'g-recaptcha-response' => ['required', 'recaptcha'],
            'invitado_email' => ['required', 'email']
        ], [
            'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.',
            'g-recaptcha-response.recaptcha' => 'El reCAPTCHA no es válido. Inténtalo de nuevo.',
            'invitado_email.required' => 'El campo de email esta vacio, por favor completalo',
            'invitado_email.email' => 'Por favor usa un formato de correo valido con @'
        ]);

        if ($validator->fails()) {
            if ($validator->errors()->has('g-recaptcha-response')) {
                \Illuminate\Support\Facades\Log::channel('login')->alert('[CAPTCHA_FAILED] Invitado falló o ignoró el reCAPTCHA', [
                    'email_intento' => $request->invitado_email ?? 'No proporcionado',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }

            return back()->withErrors($validator)->withInput();
        }
        
        \Illuminate\Support\Facades\Log::channel('login')->info('[CAPTCHA_PASSED] Invitado resolvió el reCAPTCHA correctamente', [
            'email' => $request->invitado_email,
            'ip' => $request->ip()
        ]);

        $otp = rand(100000, 999999);
        session()->put('invitado_otp_code', $otp);
        session()->put('invitado_otp_email', $request->invitado_email);
    
        \Illuminate\Support\Facades\Mail::to($request->invitado_email)->send(new \App\Mail\OtpMail($otp));

        \Illuminate\Support\Facades\Log::channel('login')->info('[OTP_GENERATED] Se ha solicitado un código OTP para Invitado', [
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
            
            Log::channel('login')->info('[OTP_VERIFIED] Invitado verificó su OTP con éxito', [
                'email' => $emailInvitado,
                'ip' => $request->ip()
            ]);

            session()->forget(['invitado_otp_code', 'invitado_otp_email']);
            
            return redirect()->route('dashboard');
        }

        Log::channel('login')->warning('[OTP_FAILED] Código OTP incorrecto insertado por invitado', [
            'email_intento' => $emailInvitado,
            'codigo_ingresado' => $request->otp_input,
            'ip' => $request->ip()
        ]);

        return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
    }

    public function showUsuarioOtpForm()
    {
        if (!session()->has('auth_user_otp_code') && !session()->has('registro_otp_code')) {
            return redirect()->route('login');
        }
        return view('auth.usuario-otp');
    }

    public function verifyUsuarioOtp(Request $request)
    {
        $request->validate(['otp_input' => 'required|digits:6']);

        if ($request->session()->has('registro_temporal')) {
            $emailIntento = \Illuminate\Support\Facades\Crypt::decrypt(
                $request->session()->get('registro_temporal')
            )['email'] ?? 'Desconocido';

            if ($request->otp_input != $request->session()->get('registro_otp_code')) {
                Log::channel('login')->warning('[REGISTER_OTP_FAILED] Código OTP de registro incorrecto', [
                    'email_intento'   => $emailIntento,
                    'codigo_ingresado' => $request->otp_input,
                    'ip'              => $request->ip()
                ]);

                return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
            }

            $datosUser = \Illuminate\Support\Facades\Crypt::decrypt(
                $request->session()->get('registro_temporal')
            );

            $user = User::create([
                'name'     => $datosUser['name'],
                'email'    => $datosUser['email'],
                'password' => $datosUser['password'],
                'is_admin' => false,
            ]);

            Log::channel('login')->info('[REGISTER_SUCCESS] Usuario normal registrado en DB tras verificar OTP de correo', [
                'user_id'  => $user->id,
                'email'    => $user->email,
                'ip'       => $request->ip()
            ]);

            $request->session()->forget(['registro_temporal', 'registro_otp_code']);
            $request->session()->save();

            Auth::login($user);

            return redirect()->route('dashboard');
        }

        $emailUsuario = session()->get('auth_pre_user_email');

        if ($request->otp_input == session()->get('auth_user_otp_code')) {
            
            $user = User::find(session()->get('auth_pre_user_id'));

            if ($user) {
                Auth::login($user);

                Log::channel('login')->info('[2FA_VERIFIED] Usuario autenticado completamente mediante OTP', [
                    'email' => $user->email,
                    'ip'    => $request->ip()
                ]);

                session()->forget(['auth_user_otp_code', 'auth_pre_user_id', 'auth_pre_user_email']);
                
                session()->save();

                return redirect()->route('dashboard');
            }
        }

        Log::channel('login')->warning('[2FA_FAILED] Código OTP de segundo factor incorrecto', [
            'email_usuario'    => $emailUsuario,
            'codigo_ingresado' => $request->otp_input,
            'ip'               => $request->ip()
        ]);

        return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
    }
}