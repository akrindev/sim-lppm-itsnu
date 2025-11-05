<x-mail::message>
    # ✅ Anggota Tim Menerima Undangan

    Halo **{{ $recipientName }}**,

    **{{ $memberName }}** telah menerima undangan untuk bergabung sebagai anggota tim.

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Anggota yang menerima:** {{ $memberName }}
    - **Tanggal Penerimaan:** {{ $acceptedDate }}

    Tim proposal Anda semakin lengkap. Harap pastikan semua anggota tim telah memberikan persetujuan sebelum proposal
    dapat diajukan.

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
