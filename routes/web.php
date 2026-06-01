<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\TwoFactorController;

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

Route::get('/dashboard', function () {
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

Route::post('/invitado-checkpoint', function (Request $request) {
    $request->validate([
        'g-recaptcha-response' => ['required', 'recaptcha'],
    ], [
        'g-recaptcha-response.required' => 'Por favor, completa el reCAPTCHA para demostrar que no eres un robot.',
    ]);

    $request->session()->put('invitado_verificado', true);

    return redirect()->route('dashboard');
})->name('invitado.verify');

Route::get('/dashboard', function (Request $request) {
    // Si no está logueado Y tampoco ha pasado el captcha de invitado, lo mandamos al checkpoint
    if (!auth()->check() && !$request->session()->has('invitado_verificado')) {
        return redirect()->route('invitado.checkpoint');
    }
    return view('dashboard');
})->name('dashboard');

require __DIR__.'/auth.php';
