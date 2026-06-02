<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\OtpMailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    if (!auth()->check() && !$request->session()->has('invitado_verificado')) {
        return redirect()->route('invitado.checkpoint');
    }

    if (!auth()->check() && $request->session()->has('invitado_verificado')) {
        
        $tiempoActual = time();
        $tiempoInactividadPermitido = 300;

        if ($request->session()->has('invitado_ultima_actividad')) {
            $ultimaActividad = $request->session()->get('invitado_ultima_actividad');
            
            if (($tiempoActual - $ultimaActividad) > $tiempoInactividadPermitido) {
                $request->session()->forget(['invitado_verificado', 'invitado_ultima_actividad']);
                return redirect()->route('invitado.checkpoint')->withErrors(['invitado_email' => 'Tu sesión de invitado expiró por inactividad.']);
            }
        }

        $request->session()->put('invitado_ultima_actividad', $tiempoActual);
    }

    return view('dashboard');
})->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/2fa-register', [TwoFactorController::class, 'showRegister'])->name('2fa.register');
Route::post('/2fa-register', [TwoFactorController::class, 'saveRegister'])->name('2fa.register.save');

Route::get('/2fa-challenge', [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
Route::post('/2fa-challenge', [TwoFactorController::class, 'verifyChallenge'])->name('2fa.challenge.verify');

Route::get('/invitado-checkpoint', function () {
    return view('auth.invitado-captcha');
})->name('invitado.checkpoint');

Route::post('/invitado-checkpoint', [OtpMailController::class, 'processInvitadoCheckpoint'])->name('invitado.verify');

Route::get('/invitado-otp', [OtpMailController::class, 'showInvitadoOtpForm'])->name('invitado.otp');

Route::post('/invitado-otp', [OtpMailController::class, 'verifyInvitadoOtp'])->name('invitado.otp.submit');

Route::get('/verificar-usuario', [OtpMailController::class, 'showUsuarioOtpForm'])->name('usuario.otp');
Route::post('/verificar-usuario', [OtpMailController::class, 'verifyUsuarioOtp'])->name('usuario.otp.submit');

require __DIR__.'/auth.php';