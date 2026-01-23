<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\TwoFactorChallengeController;
use App\Livewire\Actions\Logout;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::livewire('login', Login::class)->name('login');
    Route::livewire('forgot-password', ForgotPassword::class)->name('password.request');
    Route::livewire('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::livewire('verify-email', VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', [\Laravel\Fortify\Http\Controllers\ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');
});

// Two-factor authentication challenge routes
Route::get('two-factor-challenge', [TwoFactorChallengeController::class, 'create'])
    ->name('two-factor.login');

Route::post('two-factor-challenge', [TwoFactorChallengeController::class, 'store'])
    ->name('two-factor.login.store');

Route::post('logout', Logout::class)
    ->name('logout');
