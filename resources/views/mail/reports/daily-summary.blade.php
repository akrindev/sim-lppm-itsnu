<x-mail::message>
    # Laporan Ringkas Harian

    Halo **{{ $recipientName }}**,

    Berikut adalah laporan ringkas untuk hari **{{ $date }}**:

    @if ($role === 'admin lppm')
        ## 📊 Ringkasan Admin LPPM

        - **Proposal baru:** {{ $data['pending_proposals'] ?? 0 }}
        - **Dalam review:** {{ $data['under_review'] ?? 0 }}
        - **Menunggu keputusan:** {{ $data['awaiting_decision'] ?? 0 }}
        - **Review tertunda:** {{ $data['total_reviews_pending'] ?? 0 }}
    @elseif($role === 'kepala lppm')
        ## 📊 Ringkasan Kepala LPPM

        - **Menunggu approval awal:** {{ $data['pending_initial_approval'] ?? 0 }}
        - **Siap assignment reviewer:** {{ $data['needing_reviewer_assignment'] ?? 0 }}
        - **Menunggu keputusan final:** {{ $data['awaiting_final_decision'] ?? 0 }}
        - **Selesai hari ini:** {{ $data['completed_today'] ?? 0 }}
    @elseif($role === 'dekan')
        ## 📊 Ringkasan Dekan

        - **Menunggu review:** {{ $data['pending_submissions'] ?? 0 }}
        - **Disetujui hari ini:** {{ $data['approved_today'] ?? 0 }}
        - **Ditolak hari ini:** {{ $data['rejected_today'] ?? 0 }}
    @elseif($role === 'reviewer')
        ## 📊 Ringkasan Review Anda

        - **Penugasan tertunda:** {{ $data['pending_assignments'] ?? 0 }}
        - **Selesai hari ini:** {{ $data['completed_today'] ?? 0 }}
    @else
        Tidak ada laporan untuk hari ini.
    @endif

    <x-mail::button :url="$url">
        Lihat Dashboard
    </x-mail::button>

    Terima kasih.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
