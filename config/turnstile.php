<?php

return [
    'site_key' => app()->isLocal() ? null : env('TURNSTILE_SITE_KEY'),
    'secret_key' => app()->isLocal() ? null : env('TURNSTILE_SECRET_KEY'),
];
