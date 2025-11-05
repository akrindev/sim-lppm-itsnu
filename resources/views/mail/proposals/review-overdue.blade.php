<x-mail::message>
    # 🚨 URGENT: Review Overdue

    Halo **{{ $reviewerName }}**,

    ⚠️ **PENTING:** Review Anda telah melewati batas waktu sebesar **{{ $daysOverdue }} hari** untuk proposal
    **{{ $proposalType }}** berikut:

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Hari Melewati Batas:** {{ $daysOverdue }} hari

    Ini adalah prioritas tinggi dan memerlukan penyelesaian segera untuk tidak menghambat proses proposal. Silakan
    lakukan review dan submit hasilnya segera.

    <x-mail::button :url="$url" color="error">
        Lihat Proposal Sekarang
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
