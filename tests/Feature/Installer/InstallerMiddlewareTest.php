<?php

declare(strict_types=1);

use App\Http\Middleware\InstallerMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

it('bootstraps an installer key when missing', function () {
    config(['app.key' => null]);

    $lockFile = storage_path('app/.installed');
    $installerKeyFile = storage_path('app/.installer_key');

    if (File::exists($lockFile)) {
        File::delete($lockFile);
    }

    if (File::exists($installerKeyFile)) {
        File::delete($installerKeyFile);
    }

    $middleware = new InstallerMiddleware;
    $request = Request::create('/install', 'GET');

    $response = $middleware->handle($request, fn () => response('ok', 200));

    expect(config('app.key'))->not->toBeEmpty()
        ->and(File::exists($installerKeyFile))->toBeTrue()
        ->and($response->getStatusCode())->toBe(200);

    if (File::exists($installerKeyFile)) {
        File::delete($installerKeyFile);
    }
});
