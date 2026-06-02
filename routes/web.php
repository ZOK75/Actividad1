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

// --- PÁGINA DE BIENVENIDA ---
Route::get('/', function () {
    return view('welcome');
});


// --- PROTECCIÓN DEL DASHBOARD (ÚNICA Y BLINDADA) ---
Route::get('/dashboard', function (Request $request) {
    // Si NO está logueado como usuario/admin Y TAMPOCO tiene el pase de invitado verificado, al checkpoint
    if (!auth()->check() && !$request->session()->has('invitado_verificado')) {
        return redirect()->route('invitado.checkpoint');
    }
    return view('dashboard');
})->name('dashboard');


// --- PERFIL DE USUARIO (BREEZE) ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// --- FLUJO GOOGLE AUTHENTICATOR (ADMINISTRADOR) ---
Route::get('/2fa-register', [TwoFactorController::class, 'showRegister'])->name('2fa.register');
Route::post('/2fa-register', [TwoFactorController::class, 'saveRegister'])->name('2fa.register.save');

Route::get('/2fa-challenge', [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
Route::post('/2fa-challenge', [TwoFactorController::class, 'verifyChallenge'])->name('2fa.challenge.verify');


// --- FLUJO ACTUALIZADO DEL INVITADO (CON MAILTRAP) ---
// 1. Vista del formulario inicial (Correo + reCAPTCHA)
Route::get('/invitado-checkpoint', function () {
    return view('auth.invitado-captcha');
})->name('invitado.checkpoint');

// 2. Procesa el formulario anterior y envía el correo OTP
Route::post('/invitado-checkpoint', [OtpMailController::class, 'processInvitadoCheckpoint'])->name('invitado.verify');

// 3. Vista para introducir el código OTP del correo
Route::get('/invitado-otp', [OtpMailController::class, 'showInvitadoOtpForm'])->name('invitado.otp');

// 4. Procesa el código OTP y le da acceso definitivo al Dashboard
Route::post('/invitado-otp', [OtpMailController::class, 'verifyInvitadoOtp'])->name('invitado.otp.submit');


// --- AUTENTICACIÓN DE BREEZE ---
require __DIR__.'/auth.php';