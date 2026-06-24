<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class TwoFactorController extends Controller
{
    public function showRegister(Request $request)
    {
        if ($request->session()->has('registro_temporal')) {
            $datosUser = \Illuminate\Support\Facades\Crypt::decrypt(
                $request->session()->get('registro_temporal')
            );

            $google2fa = app('pragmarx.google2fa');

            $secretKey = $datosUser['google2fa_secret'];

            $qrCodeUrl   = $google2fa->getQRCodeUrl('Actividad1-Admin', $datosUser['email'], $secretKey);
            $qrCodeImage = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCodeUrl);

            return view('auth.2fa-register', compact('qrCodeImage', 'secretKey'));
        }

        $userId = $request->session()->get('2fa_user_id');
        if (!$userId) return redirect()->route('login');

        $user     = User::find($userId);
        $google2fa = app('pragmarx.google2fa');

        $secretKey = $google2fa->generateSecretKey();
        $request->session()->put('2fa_secret_temp', $secretKey);

        $qrCodeUrl   = $google2fa->getQRCodeUrl('Actividad1-Admin', $user->email, $secretKey);
        $qrCodeImage = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCodeUrl);

        return view('auth.2fa-register', compact('qrCodeImage', 'secretKey'));
    }

    public function saveRegister(Request $request)
    {
        $request->validate([
            'one_time_password' => 'required|digits:6',
            'email_otp_code'    => 'required|digits:6'
        ], [
            'one_time_password.required' => 'El código del celular es obligatorio.',
            'email_otp_code.required'    => 'El código enviado al correo es obligatorio.'
        ]);

        if ($request->session()->has('registro_temporal')) {
            $datosUser = \Illuminate\Support\Facades\Crypt::decrypt(
                $request->session()->get('registro_temporal')
            );
            $secretKey   = $datosUser['google2fa_secret'];
            $emailAdmin  = $datosUser['email'];

            $google2fa = app('pragmarx.google2fa');
            $valid = $google2fa->verifyKey($secretKey, $request->one_time_password);

            if (!$valid) {
                Log::channel('login')->warning('[2FA_REGISTER_FAILED] Token de Google Authenticator incorrecto durante el registro de Admin', [
                    'email_admin' => $emailAdmin,
                    'ip'          => $request->ip()
                ]);

                return back()->withErrors(['one_time_password' => 'El código de Google Authenticator es inválido.']);
            }

            if ($request->email_otp_code != $request->session()->get('registro_otp_code')) {
                Log::channel('login')->warning('[2FA_REGISTER_FAILED] Código OTP de correo incorrecto durante registro de Administrador', [
                    'email_admin' => $emailAdmin,
                    'ip'          => $request->ip()
                ]);

                return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto.']);
            }

            $user = User::create([
                'name'           => $datosUser['name'],
                'email'          => $datosUser['email'],
                'password'       => $datosUser['password'],  
                'is_admin'       => true,
                'google2fa_secret' => $secretKey,            
            ]);

            Log::channel('login')->info('[2FA_REGISTER_SUCCESS] Administrador registrado en DB y Google Authenticator vinculado con éxito', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'ip'      => $request->ip()
            ]);

            $request->session()->forget(['registro_temporal', 'registro_otp_code']);
            $request->session()->save();

            Auth::login($user);
            return redirect()->route('dashboard');
        }

        $userId    = $request->session()->get('2fa_user_id');
        $secretKey = $request->session()->get('2fa_secret_temp');
        $user      = User::find($userId);

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($secretKey, $request->one_time_password);

        if (!$valid) {
            Log::channel('login')->warning('[2FA_REGISTER_FAILED] Token de Google Authenticator incorrecto durante el registro', [
                'email_admin' => $user ? $user->email : 'Desconocido',
                'ip'          => $request->ip()
            ]);

            return back()->withErrors(['one_time_password' => 'El código de Google Authenticator es inválido.']);
        }

        if ($request->email_otp_code != $request->session()->get('auth_user_otp_code')) {
            Log::channel('login')->warning('[2FA_REGISTER_FAILED] Código OTP de correo incorrecto durante registro de Administrador', [
                'email_admin' => $user ? $user->email : 'Desconocido',
                'ip'          => $request->ip()
            ]);

            return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto.']);
        }

        $user->google2fa_secret = $secretKey;
        $user->save();

        Auth::login($user);

        Log::channel('login')->info('[2FA_REGISTER_SUCCESS] Administrador vinculó su Google Authenticator con éxito', [
            'email' => $user->email,
            'ip'    => $request->ip()
        ]);

        $request->session()->forget(['2fa_user_id', '2fa_secret_temp']);
        return redirect()->route('dashboard');
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

        $google2fa = app('pragmarx.google2fa');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        if (!$valid) {
            Log::channel('login')->warning('[GOOGLE_2FA_FAILED] Token dinámico de Google Authenticator inválido proporcionado por Administrador', [
                'email_administrador' => $user ? $user->email : 'Desconocido',
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['one_time_password' => 'El código de Google Authenticator es inválido.']);
        }

        if ($request->email_otp_code != $request->session()->get('auth_user_otp_code')) {
            Log::channel('login')->warning('[GOOGLE_2FA_FAILED] Código de correo incorrecto para Administrador en el Login corporativo', [
                'email_administrador' => $user ? $user->email : 'Desconocido',
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['email_otp_code' => 'El código de correo electrónico es incorrecto.']);
        }

        Auth::login($user);

        Log::channel('login')->info('[GOOGLE_2FA_VERIFIED] Administrador autenticado plenamente mediante doble factor corporativo', [
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        $request->session()->forget(['2fa_user_id', 'auth_user_otp_code']);
        $request->session()->save();

        return redirect()->route('dashboard');
    }
}