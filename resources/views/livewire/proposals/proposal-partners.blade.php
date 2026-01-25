<div>
    <div class="mb-3 card">
        <div class="card-header">
            <h3 class="card-title">4.1 Mitra Kerjasama</h3>
        </div>
        <div class="card-body">
            @if ($proposal->partners->isEmpty())
                <p class="text-muted">Belum ada mitra yang ditambahkan</p>
            @else
                <div class="table-responsive">
                    <table class="table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama Mitra</th>
                                <th>Institusi</th>
                                <th>Email</th>
                                <th>Negara</th>
                                <th>Alamat</th>
                                <th>Tipe</th>
                                <th>Surat Kesanggupan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proposal->partners as $partner)
                                <tr>
                                    <td>
                                        <div class="font-weight-medium">{{ $partner->name }}</div>
                                    </td>
                                    <td>
                                        @if ($partner->institution)
                                            <div class="d-flex align-items-center">
                                                <x-lucide-building class="me-1 text-muted icon" />
                                                {{ $partner->institution }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->email)
                                            <a href="mailto:{{ $partner->email }}" class="text-reset">
                                                <div class="d-flex align-items-center">
                                                    <x-lucide-mail class="me-1 text-muted icon" />
                                                    {{ $partner->email }}
                                                </div>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->country)
                                            <div class="d-flex align-items-center">
                                                <x-lucide-map-pin class="me-1 text-muted icon" />
                                                {{ $partner->country }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($partner->address)
                                            <div class="text-truncate" style="max-width: 200px;"
                                                title="{{ $partner->address }}">
                                                {{ $partner->address }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <x-tabler.badge color="blue">
                                            {{ $partner->type ?? 'External' }}
                                        </x-tabler.badge>
                                    </td>
                                    <td>
                                        @if ($partner->hasMedia('commitment_letter'))
                                            <a href="{{ $partner->getFirstMediaUrl('commitment_letter') }}"
                                                target="_blank" class="btn btn-sm btn-primary">
                                                <x-lucide-download class="icon" />
                                                Unduh
                                            </a>
                                        @else
                                            <x-tabler.badge color="yellow">
                                                <x-lucide-file-x class="me-1 icon" />
                                                Tidak Ada
                                            </x-tabler.badge>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
