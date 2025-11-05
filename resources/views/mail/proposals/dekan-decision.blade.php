<x-mail::message>
    # Keputusan Dekan

    Halo **{{ $recipientName }}**,

    @if ($isApproved)
        Dekan telah **menyetujui** proposal **{{ $proposalType }}** berikut:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Dekan:** {{ $dekanName }}
        - **Tanggal Keputusan:** {{ $decisionDate }}

        Proposal ini telah diteruskan ke Kepala LPPM untuk tahap selanjutnya yaitu penentuan reviewer.
    @else
        Dekan meninjau proposal **{{ $proposalType }}** berikut dan meminta **persetujuan dari anggota tim**:

        ## Detail Proposal

        - **Judul:** {{ $proposalTitle }}
        - **Dekan:** {{ $dekanName }}
        - **Tanggal Keputusan:** {{ $decisionDate }}

        Mohon kepada seluruh anggota tim untuk memeriksa dan memberikan persetujuan melalui sistem.
    @endif

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
