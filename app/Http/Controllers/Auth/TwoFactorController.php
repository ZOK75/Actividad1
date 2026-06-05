<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Aseguramos la importación del Facade de logs
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    public function showRegister(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        $google2fa = app('pragmarx.google2fa');

        $secretKey = $google2fa->generateSecretKey();
        $request->session()->put('2fa_secret_temp', $secretKey);

        $qrCodeUrl = $google2fa->getQRCodeUrl('Actividad1-Admin', $user->email, $secretKey);

        $qrCodeImage = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCodeUrl);

        return view('auth.2fa-register', compact('qrCodeImage', 'secretKey'));
    }

    public function saveRegister(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        $secretKey = $request->session()->get('2fa_secret_temp');
        $user = User::find($userId);
        
        $request->validate([
            'one_time_password' => 'required|digits:6',
            'email_otp_code' => 'required|digits:6'
        ], [
            'one_time_password.required' => 'El código del celular es obligatorio.',
            'email_otp_code.required' => 'El código enviado al correo es obligatorio.'
        ]);

        //  CONFIGURACIÓN - ERROR 1: Falló el código de Correo enviado al Administrador
        if ($request->email_otp_code != $request->session()->get('auth_user_otp_code')) {
            Log::channel('login')->warning('[2FA_REGISTER_FAILED] Código de correo incorrecto durante registro 2FA de Administrador', [
                'email_admin' => $user ? $user->email : 'Desconocido',
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto. Verifícalo en Mailtrap.']);
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($secretKey, $request->one_time_password);

        if ($valid) {
            $user->google2fa_secret = $secretKey;
            $user->save();

            Auth::login($user);

            //  CONFIGURACIÓN - ÉXITO: Administrador configuró exitosamente su app móvil y correo
            Log::channel('login')->info('[2FA_REGISTER_SUCCESS] Administrador vinculó su Google Authenticator con éxito', [
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            $request->session()->forget(['2fa_user_id', '2fa_secret_temp']);
            return redirect()->route('dashboard');
        }

        // CONFIGURACIÓN - ERROR 2: Falló el token de la aplicación móvil de Google
        Log::channel('login')->warning('[2FA_REGISTER_FAILED] Token de Google Authenticator incorrecto durante el registro', [
            'email_admin' => $user ? $user->email : 'Desconocido',
            'ip' => $request->ip()
        ]);

        return back()->withErrors(['one_time_password' => 'Código OTP incorrecto. Intenta de nuevo.']);
    }

    public function showChallenge(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) return redirect()->route('login');
        return view('auth.2fa-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');

        $request->validate([
            'one_time_password' => 'required|digits:6',
            'email_otp_code' => 'required|digits:6'
        ], [
            'one_time_password.required' => 'El código del celular es obligatorio.',
            'email_otp_code.required' => 'El código enviado al correo es obligatorio.'
        ]);

        $user = User::find($userId);

        //  RETO - ERROR 1: El administrador ingresó mal el código que le llegó al correo
        if ($request->email_otp_code != $request->session()->get('auth_user_otp_code')) {
            Log::channel('login')->warning('[GOOGLE_2FA_FAILED] Código de correo incorrecto para Administrador en el Login corporativo', [
                'email_administrador' => $user ? $user->email : 'Desconocido',
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto. Verifícalo en Mailtrap.']);
        }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            Auth::login($user);

            // RETO - ÉXITO: Administrador pasó los dos controles de seguridad con éxito (Correo + App móvil)
            Log::channel('login')->info('[GOOGLE_2FA_VERIFIED] Administrador autenticado plenamente mediante doble factor corporativo', [
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            $request->session()->forget(['2fa_user_id', 'auth_user_otp_code']);
            $request->session()->save();

            return redirect()->route('dashboard');
        }

        // RETO - ERROR 2: El administrador ingresó mal el token dinámico de Google Authenticator
        Log::channel('login')->warning('[GOOGLE_2FA_FAILED] Token dinámico de Google Authenticator inválido proporcionado por Administrador', [
            'email_administrador' => $user ? $user->email : 'Desconocido',
            'ip' => $request->ip()
        ]);

        return back()->withErrors(['one_time_password' => 'El código de Google Authenticator es inválido.']);
    }
}