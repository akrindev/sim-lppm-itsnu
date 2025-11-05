<x-mail::message>
    # Ditunjuk sebagai Reviewer

    Halo **{{ $reviewerName }}**,

    Anda telah ditugaskan sebagai reviewer untuk proposal **{{ $proposalType }}** berikut:

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Batas Waktu Review:** {{ $reviewDeadline }}

    Silakan lakukan review sesuai dengan panduan reviewer yang tersedia di sistem.

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas kontribusi Anda sebagai reviewer.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
