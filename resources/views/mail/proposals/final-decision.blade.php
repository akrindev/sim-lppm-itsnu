<x-mail::message>
    # Keputusan Akhir

    Halo **{{ $recipientName }}**,

    @if ($decision === 'approved')
        Kepala LPPM telah **menyetujui** proposal **{{ $proposalType }}** berikut:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Kepala LPPM:** {{ $kepalaLppmName }}
        - **Tanggal Keputusan:** {{ $decisionDate }}

        🎉 **Selamat!** Proposal Anda telah melalui semua tahap review dan mendapat persetujuan final.
    @elseif($decision === 'rejected')
        Kepala LPPM meninjau proposal **{{ $proposalType }}** berikut dan mengambil keputusan untuk **menolaknya**:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Kepala LPPM:** {{ $kepalaLppmName }}
        - **Tanggal Keputusan:** {{ $decisionDate }}

        Mohon jangan menyerah dan silakan memperbaiki proposal Anda berdasarkan feedback yang diberikan.
    @elseif($decision === 'revision_needed')
        Kepala LPPM meninjau proposal **{{ $proposalType }}** berikut dan meminta **perbaikan/revisi**:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Kepala LPPM:** {{ $kepalaLppmName }}
        - **Tanggal Keputusan:** {{ $decisionDate }}

        Mohon untuk segera memperbaiki proposal sesuai dengan catatan dan rekomendasi yang diberikan.
    @else
        Kepala LPPM telah mengambil keputusan final terhadap proposal **{{ $proposalType }}** berikut:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Kepala LPPM:** {{ $kepalaLppmName }}
        - **Tanggal Keputusan:** {{ $decisionDate }}

        Mohon melihat detail keputusan dan catatan yang diberikan melalui sistem.
    @endif

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
