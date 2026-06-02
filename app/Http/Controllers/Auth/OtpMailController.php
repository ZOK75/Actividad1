<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class OtpMailController extends Controller
{
    // Procesa el correo y el reCAPTCHA del invitado
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

        // Generamos un código OTP de 6 dígitos al azar
        $otp = rand(100000, 999999);

        // Guardamos de forma temporal el código en la sesión de la laptop
        session()->put('invitado_otp_code', $otp);
        session()->put('invitado_otp_email', $request->invitado_email);
        
        // Enviamos el correo mediante Mailtrap
        Mail::to($request->invitado_email)->send(new OtpMail($otp));

        // Redirigimos a la pantalla del OTP
        return redirect()->route('invitado.otp');
    }

    // Muestra la vista donde el invitado escribe el código de Mailtrap
    public function showInvitadoOtpForm()
    {
        // Seguridad: Si intentan entrar a la mala sin pasar el captcha, los regresamos
        if (!session()->has('invitado_otp_code')) {
            return redirect()->route('invitado.checkpoint');
        }
        return view('auth.invitado-otp');
    }

    // Compara el código que el invitado escribió
    public function verifyInvitadoOtp(Request $request)
    {
        $request->validate(['otp_input' => 'required|digits:6']);

        if ($request->otp_input == session()->get('invitado_otp_code')) {
            // El código es correcto, le damos el pase oficial de Invitado
            session()->put('invitado_verificado', true);
            
            // Limpiamos los datos temporales del OTP de la sesión
            session()->forget(['invitado_otp_code', 'invitado_otp_email']);
            
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
    }
}