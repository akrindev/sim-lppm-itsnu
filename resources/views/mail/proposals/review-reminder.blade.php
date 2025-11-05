<x-mail::message>
    # ⏰ Pengingat: Review Akan Jatuh Tempo

    Halo **{{ $reviewerName }}**,

    Ini adalah pengingat bahwa review Anda akan jatuh tempo dalam **{{ $daysRemaining }} hari** untuk proposal
    **{{ $proposalType }}** berikut:

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Sisa Waktu:** {{ $daysRemaining }} hari

    Silakan segera lakukan review sesuai dengan panduan reviewer yang tersedia di sistem.

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas dedikasi Anda sebagai reviewer.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
