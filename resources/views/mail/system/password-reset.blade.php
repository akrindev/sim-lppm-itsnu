<x-mail::message>
    # 🔑 Reset Password Anda

    Halo **{{ $userName }}**,

    Anda telah meminta untuk mereset password akun Anda.

    Link reset password ini akan kadaluarsa dalam **60 menit**.

    <x-mail::button :url="$resetUrl">
        Reset Password
    </x-mail::button>

    Jika Anda tidak meminta reset password, abaikan email ini.

    Terima kasih.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
