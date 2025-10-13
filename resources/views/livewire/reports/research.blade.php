<x-layouts.app :title="__('Laporan Penelitian')" :pageTitle="__('Laporan Penelitian')">
    <x-slot:pageHeader>
        <div class="mb-2 text-secondary">
            {{ __('Akses khusus Admin LPPM dan Rektor untuk memantau perkembangan laporan penelitian.') }}
        </div>
        <div class="text-muted small">
            {{ __('Pilih periode untuk melihat jadwal dan rangkuman capaian laporan penelitian.') }}
        </div>
    </x-slot:pageHeader>

    <x-slot:pageActions>
        <div class="btn-list">
            @foreach ($periods as $availablePeriod)
                <button type="button"
                    wire:click="setPeriod('{{ $availablePeriod }}')"
                    class="btn {{ $period === $availablePeriod ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $availablePeriod }}
                </button>
            @endforeach
        </div>
    </x-slot:pageActions>

    <div class="mb-3 row row-deck">
            @foreach ($summary as $card)
            <div class="col-sm-6 col-xl-4">
                <div class="card">
                    <div class="d-flex align-items-center gap-3 card-body">
                        <span class="avatar avatar-rounded {{ $card['variant'] }}">
                            <i class="ti ti-{{ $card['icon'] }}"></i>
                        </span>
                        <div>
                            <h3 class="mb-1 lh-1">{{ $card['value'] }}</h3>
                            <div class="text-secondary">{{ $card['label'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Linimasa Laporan') }} — {{ $period }}</h3>
        </div>
        <div class="card-body">
            <div class="timeline">
                @forelse ($milestones as $milestone)
                    <div class="timeline-item">
                        <div class="timeline-time">{{ $milestone['title'] }}</div>
                        <div class="bg-primary timeline-badge"></div>
                        <div class="timeline-content">
                            <div class="mb-1 fw-semibold">{{ $milestone['description'] }}</div>
                            <div class="text-secondary small">
                                {{ __('Jumlah terkait:') }}
                                <span class="bg-primary-lt ms-1 text-primary badge">{{ $milestone['value'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-5 text-secondary text-center">
                        {{ __('Belum ada data linimasa untuk periode ini.') }}
                    </div>
                @endforelse
            </div>
        </div>
        <div class="text-secondary card-footer">
            {{ __('Data ini merupakan contoh. Integrasi selanjutnya akan menampilkan statistik penelitian aktual.') }}
        </div>
    </div>
</x-layouts.app>
