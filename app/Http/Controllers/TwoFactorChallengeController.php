<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\TwoFactorAuthenticationProvider;

class TwoFactorChallengeController extends Controller
{
    /**
     * Show the two-factor authentication challenge view.
     */
    public function create(Request $request)
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('livewire.auth.two-factor-challenge');
    }

    /**
     * Attempt to verify the two-factor authentication challenge.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        $this->ensureIsNotRateLimited();

        $userId = $request->session()->get('login.id');
        $remember = $request->session()->get('login.remember', false);

        $user = (new \App\Models\User)->find($userId);

        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'code' => __('Two-factor authentication is required but could not be verified.'),
            ]);
        }

        $code = $request->input('code');
        $recoveryCode = $request->input('recovery_code');

        if (isset($recoveryCode)) {
            $valid = $this->verifyRecoveryCode($user, $recoveryCode);
        } else {
            $valid = $this->verifyCode($user, $code);
        }

        if (! $valid) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'code' => __('The provided two factor authentication code was invalid.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        $request->session()->forget('login.id');
        $request->session()->forget('login.remember');

        Auth::login($user, $remember);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Ensure the two-factor authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'code' => __('Too many failed attempts. Please try again later.'),
        ]);
    }

    /**
     * Get the two-factor authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return 'two-factor-auth:' . request()->ip();
    }

    /**
     * Verify the two-factor authentication code.
     */
    protected function verifyCode($user, string $code): bool
    {
        if (! $user->two_factor_secret) {
            return false;
        }

        try {
            $secret = decrypt($user->two_factor_secret);
            return app(TwoFactorAuthenticationProvider::class)->verify(
                $secret,
                $code
            );
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // If decryption fails, the two-factor secret is corrupted or invalid
            return false;
        }
    }

    /**
     * Verify the two-factor authentication recovery code.
     */
    protected function verifyRecoveryCode($user, string $recoveryCode): bool
    {
        if (! $user->two_factor_recovery_codes) {
            return false;
        }

        try {
            $decodedCodes = decrypt($user->two_factor_recovery_codes);
            $codes = json_decode($decodedCodes, true);

            if (! is_array($codes)) {
                return false;
            }

            foreach ($codes as $index => $code) {
                if (hash_equals($code, $recoveryCode)) {
                    // Remove the used recovery code
                    unset($codes[$index]);
                    $user->forceFill([
                        'two_factor_recovery_codes' => encrypt(json_encode(array_values($codes))),
                    ])->save();

                    return true;
                }
            }

            return false;
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // If decryption fails, the two-factor data is corrupted or invalid
            return false;
        }
    }
}
