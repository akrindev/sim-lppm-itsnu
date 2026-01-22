<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        if (! $value) {
            $fail('Konfirmasi keamanan diperlukan.');

            return;
        }

        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()->timeout(10)->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => config('turnstile.secret_key'),
            'response' => $value,
        ]);

        if (! $response->successful() || ! $response->json('success')) {
            \Illuminate\Support\Facades\Log::error('Turnstile verification failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'site_key' => config('turnstile.site_key'),
                'remoteip' => request()->ip(),
            ]);
            $fail('Verifikasi keamanan gagal. Silakan muat ulang halaman atau coba lagi.');
        }
    }
}
