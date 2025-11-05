<x-mail::message>
    # 🔐 Kode Autentikasi Dua Faktor

    Halo **{{ $userName }}**,

    Anda telah melakukan login. Berikut adalah kode verifikasi dua faktor Anda:

    <x-mail::panel>
        **Kode: {{ $code }}**
    </x-mail::panel>

    Kode ini akan berlaku selama **{{ $expiresIn }} menit**.

    **Jangan bagikan kode ini kepada siapa pun.**

    Jika Anda tidak melakukan login ini, abaikan email ini.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
