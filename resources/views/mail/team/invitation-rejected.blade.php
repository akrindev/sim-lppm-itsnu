<x-mail::message>
    # ❌ Anggota Tim Menolak Undangan

    Halo **{{ $recipientName }}**,

    **{{ $memberName }}** telah menolak undangan untuk bergabung sebagai anggota tim.

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Anggota yang menolak:** {{ $memberName }}
    - **Tanggal Penolakan:** {{ $rejectedDate }}

    Silakan cari pengganti atau hubungi anggota tim yang menolak untuk mengetahui alasannya. Proposal ini tidak dapat
    diajukan sampai semua anggota tim memberikan persetujuan.

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
