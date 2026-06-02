<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    // Muestra el QR por primera vez
    public function showRegister(Request $request)
    {
        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');

        $user = User::find($userId);
        $google2fa = app('pragmarx.google2fa');

        // Generar una nueva clave secreta
        $secretKey = $google2fa->generateSecretKey();
        $request->session()->put('2fa_secret_temp', $secretKey);

        // Crear la URL para el código QR (reemplaza 'MiApp' por el nombre de tu proyecto)
        $qrCodeUrl = $google2fa->getQRCodeUrl('Actividad1-Admin', $user->email, $secretKey);

        // Usamos una API en línea rápida para renderizar la URL en una imagen QR
        $qrCodeImage = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCodeUrl);

        return view('auth.2fa-register', compact('qrCodeImage', 'secretKey'));
    }

    // Guarda la clave tras escanear
        public function saveRegister(Request $request)
        {
            $userId = $request->session()->get('2fa_user_id');
            $secretKey = $request->session()->get('2fa_secret_temp');
            
            $request->validate([
                'one_time_password' => 'required|digits:6',
                'email_otp_code' => 'required|digits:6'
            ], [
                'one_time_password.required' => 'El código del celular es obligatorio.',
                'email_otp_code.required' => 'El código enviado al correo es obligatorio.'
            ]);

            if ($request->email_otp_code != $request->session()->get('auth_user_otp_code')) {
                return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto. Verifícalo en Mailtrap.']);
            }

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($secretKey, $request->one_time_password);

        if ($valid) {
            $user = User::find($userId);
            $user->google2fa_secret = $secretKey;
            $user->save();

            // Login oficial
            Auth::login($user);
            $request->session()->forget(['2fa_user_id', '2fa_secret_temp']);
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['one_time_password' => 'Código OTP incorrecto. Intenta de nuevo.']);
    }

    // Muestra la pantalla de reto (pedir código de 6 dígitos)
    public function showChallenge(Request $request)
    {
        if (!$request->session()->has('2fa_user_id')) return redirect()->route('login');
        return view('auth.2fa-challenge');
    }

    // Verifica el reto enviado
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

        if ($request->email_otp_code != $request->session()->get('auth_user_otp_code')) {
            return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto. Verifícalo en Mailtrap.']);
        }

        $user = User::find($userId);
        $google2fa = app('pragmarx.google2fa');
        
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if ($valid) {
            Auth::login($user);
            $request->session()->forget(['2fa_user_id', 'auth_user_otp_code']);
            $request->session()->save();

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['one_time_password' => 'El código de Google Authenticator es inválido.']);
    }
}