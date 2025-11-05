<x-mail::message>
    # ✅ Role Baru: {{ $roleLabel }}

    Halo **{{ $userName }}**,

    Selamat! Anda telah ditugaskan dengan peran: **{{ $roleLabel }}**

    ## Dengan peran ini, Anda dapat:

    @foreach ($permissions as $permission)
        - {{ $permission }}
    @endforeach

    <x-mail::button :url="$url">
        Buka SIM LPPM
    </x-mail::button>

    Jika Anda memiliki pertanyaan tentang peran baru Anda, silakan hubungi administrator.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
