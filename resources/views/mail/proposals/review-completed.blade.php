<x-mail::message>
    # Review Selesai

    Halo **{{ $recipientName }}**,

    @if ($allReviewsComplete)
        Semua reviewer telah menyelesaikan proses review untuk proposal **{{ $proposalType }}** berikut:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Reviewer Terakhir:** {{ $reviewerName }}
        - **Tanggal Selesai:** {{ $completedDate }}

        ✅ **Proposal ini sekarang menunggu keputusan akhir dari Kepala LPPM.**
    @else
        Reviewer **{{ $reviewerName }}** telah menyelesaikan review untuk proposal **{{ $proposalType }}** berikut:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Reviewer:** {{ $reviewerName }}
        - **Tanggal Selesai:** {{ $completedDate }}

        Proposal ini masih menunggu review dari reviewer lainnya.
    @endif

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
