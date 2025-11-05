<x-mail::message>
    # Undangan Menjadi Anggota Tim

    Halo **{{ $inviteeName }}**,

    **{{ $inviterName }}** mengundang Anda menjadi anggota tim untuk proposal **{{ $proposalType }}** berikut:

    ## Detail Proposal

    - **Judul:** {{ $proposalTitle }}
    - **Diundang oleh:** {{ $inviterName }}
    - **Tanggal Undangan:** {{ $invitationDate }}

    Silakan lihat proposal dan terima atau tolak undangan ini.

    <x-mail::button :url="$url">
        Lihat Proposal
    </x-mail::button>

    Terima kasih atas perhatian Anda.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
