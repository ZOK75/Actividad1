<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

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

        if ($request->otp_input == session()->get('invitado_otp_code')) {
            session()->put('invitado_verificado', true);
            
            session()->forget(['invitado_otp_code', 'invitado_otp_email']);
            
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['otp_input' => 'El código OTP ingresado es incorrecto. Verifícalo en tu bandeja de Mailtrap.']);
    }
}