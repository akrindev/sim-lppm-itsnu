<x-mail::message>
    # Proposal Baru Disubmit

    Halo **{{ $recipientName }}**,

    Proposal **{{ $proposalType }}** baru telah disubmit dan menunggu persetujuan.

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Diajukan oleh:** {{ $submitterName }}
    - **Tanggal Submit:** {{ $submitDate }}

    Proposal ini sedang menunggu review dan persetujuan dari Dekan. Tim reviewer akan ditentukan kemudian.

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
