<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['app.key' => 'base64:'.base64_encode(Str::random(32))]);

        $lockPath = storage_path('app/.installed');
        if (! File::exists($lockPath)) {
            File::put($lockPath, now()->toDateTimeString());
        }
    }
}
