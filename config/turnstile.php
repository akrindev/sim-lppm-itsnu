<?php

return [
    'site_key' => env('APP_ENV') === 'local' ? null : env('TURNSTILE_SITE_KEY'),
    'secret_key' => env('APP_ENV') === 'local' ? null : env('TURNSTILE_SECRET_KEY'),
];
