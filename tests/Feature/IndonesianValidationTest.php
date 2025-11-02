<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

test('indonesian validation messages are used when locale is id', function () {
    App::setLocale('id');

    $validator = Validator::make([], [
        'email' => 'required|email',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('email'))->toBe('Bidang email wajib diisi.');
});

test('validation required message in indonesian', function () {
    App::setLocale('id');

    $validator = Validator::make([
        'name' => '',
    ], [
        'name' => 'required',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('name'))->toBe('Bidang name wajib diisi.');
});

test('validation email message in indonesian', function () {
    App::setLocale('id');

    $validator = Validator::make([
        'email' => 'invalid-email',
    ], [
        'email' => 'email',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('email'))->toBe('Bidang email harus berupa alamat email yang valid.');
});

test('validation min message in indonesian', function () {
    App::setLocale('id');

    $validator = Validator::make([
        'password' => '123',
    ], [
        'password' => 'min:8',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('password'))->toBe('Bidang password harus berisi setidaknya 8 karakter.');
});

test('validation max message in indonesian', function () {
    App::setLocale('id');

    $validator = Validator::make([
        'title' => str_repeat('a', 300),
    ], [
        'title' => 'max:255',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('title'))->toBe('Bidang title tidak boleh lebih besar dari 255 karakter.');
});

test('validation confirmed message in indonesian', function () {
    App::setLocale('id');

    $validator = Validator::make([
        'password' => 'secret',
        'password_confirmation' => 'different',
    ], [
        'password' => 'confirmed',
    ]);

    expect($validator->fails())->toBeTrue();
    expect($validator->errors()->first('password'))->toBe('Konfirmasi bidang password tidak cocok.');
});

test('validation unique message in indonesian', function () {
    App::setLocale('id');

    $validator = Validator::make([
        'email' => 'test@example.com',
    ], [
        'email' => 'unique:users,email',
    ]);

    // This test assumes the email doesn't exist in the database
    // In a RefreshDatabase context, this should pass
    expect($validator->fails())->toBeFalse();
});

test('auth language lines in indonesian', function () {
    App::setLocale('id');

    expect(__('auth.failed'))->toBe('Kredensial ini tidak cocok dengan catatan kami.');
    expect(__('auth.password'))->toBe('Kata sandi yang diberikan salah.');
});

test('pagination language lines in indonesian', function () {
    App::setLocale('id');

    expect(__('pagination.previous'))->toBe('&laquo; Sebelumnya');
    expect(__('pagination.next'))->toBe('Selanjutnya &raquo;');
});

test('password reset language lines in indonesian', function () {
    App::setLocale('id');

    expect(__('passwords.reset'))->toBe('Kata sandi Anda telah direset.');
    expect(__('passwords.sent'))->toBe('Kami telah mengirim email link reset kata sandi Anda.');
    expect(__('passwords.throttled'))->toBe('Harap tunggu sebelum mencoba lagi.');
    expect(__('passwords.token'))->toBe('Token reset kata sandi ini tidak valid.');
    expect(__('passwords.user'))->toBe('Kami tidak dapat menemukan pengguna dengan alamat email tersebut.');
});
