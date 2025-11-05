<x-mail::message>
    # ✉️ Verifikasi Email Anda

    Halo **{{ $userName }}**,

    Terima kasih telah mendaftar. Silakan verifikasi email Anda untuk melanjutkan.

    <x-mail::button :url="$verificationUrl">
        Verifikasi Email
    </x-mail::button>

    Link verifikasi ini akan kadaluarsa dalam **60 menit**.

    Jika Anda tidak membuat akun ini, abaikan email ini.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
