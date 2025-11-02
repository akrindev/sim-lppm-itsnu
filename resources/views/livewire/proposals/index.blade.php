<x-layouts.app :title="__('Proposal')" :pageTitle="__('Pengajuan Proposal')" :pageSubtitle="__('Pantau status proposal penelitian dan pengabdian terbaru.')">
    <x-slot:pageActions>
        <div class="btn-list">
            @foreach ($statusOptions as $option)
                <button type="button" wire:click="setStatusFilter('{{ $option['key'] }}')"
                    class="btn {{ $statusFilter === $option['key'] ? 'btn-primary' : 'btn-outline-primary' }}">
                    {{ $option['label'] }}
                    <x-tabler.badge :color="$statusFilter === $option['key'] ? 'white' : 'primary'" :variant="$statusFilter === $option['key'] ? 'solid' : 'light'" class="ms-2">
                        {{ $option['count'] }}
                    </x-tabler.badge>
                </button>
            @endforeach
        </div>
    </x-slot:pageActions>

    <div class="card">
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>{{ __('Judul Proposal') }}</th>
                        <th class="w-25">{{ __('Penanggung Jawab') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Tanggal Pengajuan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($proposals as $proposal)
                        <tr>
                            <td class="fw-semibold">{{ $proposal['title'] }}</td>
                            <td>{{ $proposal['owner'] }}</td>
                            <td class="text-center">
                                <x-tabler.badge :color="$proposal['status_badge']">
                                    {{ $proposal['status_label'] }}
                                </x-tabler.badge>
                            </td>
                            <td class="text-secondary text-end">
                                {{ \Carbon\Carbon::parse($proposal['submitted_at'])->translatedFormat('d F Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-secondary text-center">
                                {{ __('Belum ada proposal pada filter ini.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-secondary card-footer">
            {{ __('Data bersifat contoh. Integrasi dengan basis data akan menampilkan proposal sebenarnya.') }}
        </div>
    </div>
</x-layouts.app>
