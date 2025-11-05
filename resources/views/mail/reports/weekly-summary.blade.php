<x-mail::message>
    # Laporan Mingguan

    Halo **{{ $recipientName }}**,

    Berikut adalah laporan mingguan untuk minggu **{{ $week }} {{ $year }}**:

    @if ($role === 'dekan')
        ## 📊 Ringkasan Mingguan Dekan

        - **Total proposal minggu ini:** {{ $data['new_submissions'] ?? 0 }}
        - **Disetujui minggu ini:** {{ $data['approved_count'] ?? 0 }}
        - **Ditolak minggu ini:** {{ $data['rejected_count'] ?? 0 }}
        - **Menunggu review:** {{ $data['total_pending'] ?? 0 }}
    @elseif($role === 'kepala lppm')
        ## 📊 Ringkasan Mingguan LPPM

        - **Proposal ditugaskan minggu ini:** {{ $data['proposals_assigned'] ?? 0 }}
        - **Review selesai minggu ini:** {{ $data['reviews_completed'] ?? 0 }}
        - **Keputusan final minggu ini:** {{ $data['final_decisions'] ?? 0 }}
        - **Sedang dalam proses review:** {{ $data['under_review'] ?? 0 }}
    @elseif($role === 'rektor')
        ## 📊 Ringkasan Mingguan Universitas

        - **Total proposal:** {{ $data['total_proposals'] ?? 0 }}
        - **Selesai minggu ini:** {{ $data['completed_this_week'] ?? 0 }}
        - **Proposal penelitian:** {{ $data['total_research'] ?? 0 }}
        - **Proposal pengabdian:** {{ $data['total_community_service'] ?? 0 }}
        - **Rata-rata waktu review:** {{ $data['avg_review_time'] ?? 'N/A' }} hari
    @else
        Tidak ada laporan minggu ini.
    @endif

    <x-mail::button :url="$url">
        Lihat Dashboard
    </x-mail::button>

    Terima kasih.

    Salam,<br>
    {{ config('app.name') }}
</x-mail::message>
