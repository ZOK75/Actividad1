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
    // 1. Si NO está logueado como usuario/admin Y TAMPOCO tiene el pase de invitado, al checkpoint
    if (!auth()->check() && !$request->session()->has('invitado_verificado')) {
        return redirect()->route('invitado.checkpoint');
    }

    // 2. Si es un INVITADO (no está logueado pero tiene el pase temporal)
    if (!auth()->check() && $request->session()->has('invitado_verificado')) {
        
        // Almacenamos temporalmente que queremos mostrar la vista
        $view = view('dashboard');

        // 🔥 ¡EL TRUCO AQUÍ! Olvidamos el pase inmediatamente.
        // Como la petición actual ya pasó el IF de arriba, la vista se cargará esta vez,
        // pero si abre otra pestaña o recarga, la sesión estará limpia y lo botará.
        $request->session()->forget('invitado_verificado');

        return $view;
    }

    // 3. Si es un USUARIO REGISTRADO/ADMIN, lo dejamos pasar normal (su sesión dura lo normal)
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

require __DIR__.'/auth.php';