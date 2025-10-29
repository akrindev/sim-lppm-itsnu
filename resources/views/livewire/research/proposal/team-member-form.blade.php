<div class="card">
    <div class="card-header">
        <h3 class="card-title">Anggota Tim</h3>
    </div>

    @if ($this->teamMembers->count() > 0)
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th width="25%">Nama</th>
                        <th>Tugas</th>
                        <th>Peran</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->teamMembers as $member)
                        <tr>
                            <td>
                                <div>
                                    {{ $member->name }}
                                </div>
                                <div class="text-muted small">
                                    {{-- identity_id --}}
                                    {{ $member->identity->identity_id }}
                                </div>
                            </td>
                            <td>{{ $member->pivot->tasks ?? '—' }}</td>
                            <td>
                                <x-tabler.badge :color="$member->pivot->role === 'ketua' ? 'success' : 'info'">
                                    {{ ucfirst($member->pivot->role) }}
                                </x-tabler.badge>
                            </td>
                            <td>
                                @if ($member->pivot->status === 'accepted')
                                    <x-tabler.badge color="success">Diterima</x-tabler.badge>
                                @elseif ($member->pivot->status === 'pending')
                                    <x-tabler.badge color="warning">Menunggu</x-tabler.badge>
                                @else
                                    <x-tabler.badge color="danger">Ditolak</x-tabler.badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                Belum ada anggota tim yang ditambahkan
            </div>
        </div>
    @endif
</div>
