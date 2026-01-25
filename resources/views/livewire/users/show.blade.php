<div x-data="{}" @scroll-to-top.window="window.scrollTo({ top: 0, behavior: 'smooth' })">
    <x-slot:title>Detail Pengguna</x-slot:title>
    <x-slot:pageTitle>Detail Pengguna</x-slot:pageTitle>
    <x-slot:pageSubtitle>Lihat profil lengkap pengguna dan metadata.</x-slot:pageSubtitle>
    <x-slot:pageActions>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary" wire:navigate.hover>
            Ubah pengguna
        </a>
    </x-slot:pageActions>

    <div class="row row-cards">
        <div class="col-md-5">
            <div class="card">
                <div class="text-center card-body">
                    <span class="mb-3 avatar avatar-xl" style="background-image: url('{{ $user->profile_picture }}')">
                        @if (!$user->getFirstMedia('avatar') && !$user->identity?->profile_picture)
                            {{ $user->initials() }}
                        @endif
                    </span>
                    <h2 class="mb-1">{{ $user->name }}</h2>
                    <p class="text-secondary">{{ $user->email }}</p>
                    <div class="my-3">
                        @if ($user->roles->isNotEmpty())
                            @foreach ($user->roles as $role)
                                <x-tabler.badge color="primary"
                                    size="lg">{{ str($role->name)->title() }}</x-tabler.badge>
                            @endforeach
                        @else
                            <span class="text-secondary">Tidak ada peran</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        @if ($user->hasVerifiedEmail())
                            <x-tabler.badge color="green">Email terverifikasi</x-tabler.badge>
                        @else
                            <x-tabler.badge color="yellow">Verifikasi menunggu</x-tabler.badge>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-3 card">
                <div class="card-body">
                    <h3 class="card-title">Detail profil</h3>
                    <dl class="row row-cards">
                        <div class="col-sm-6">
                            <dt class="text-secondary">Nama</dt>
                            <dd class="fw-medium">{{ $user->name ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">Alamat email</dt>
                            <dd class="fw-medium">{{ $user->email ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">Bergabung platform</dt>
                            <dd class="fw-medium">{{ optional($user->created_at)->translatedFormat('d F Y') ?? '—' }}
                            </dd>
                        </div>
                        <div class="col-12">
                            <dt class="text-secondary">Peran</dt>
                            <dd class="fw-medium">
                                @if ($user->roles->isNotEmpty())
                                    @foreach ($user->roles as $role)
                                        <x-tabler.badge color="primary"
                                            size="lg">{{ str($role->name)->title() }}</x-tabler.badge>
                                    @endforeach
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Informasi Identitas</h3>
                    <dl class="row row-cards">
                        <div class="col-sm-6">
                            <dt class="text-secondary">ID Identitas (NIDN/NIK/NIM/NIP)</dt>
                            <dd class="fw-medium">{{ $user->identity?->identity_id ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">Tipe Identitas</dt>
                            <dd class="fw-medium">
                                @if ($user->identity?->type)
                                    <span class="text-capitalize">{{ str($user->identity->type)->title() }}</span>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">Tempat Lahir</dt>
                            <dd class="fw-medium">{{ $user->identity?->birthplace ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">Tanggal Lahir</dt>
                            <dd class="fw-medium">
                                {{ $user->identity?->birthdate ? \Illuminate\Support\Carbon::parse($user->identity->birthdate)->translatedFormat('d F Y') : '—' }}
                            </dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">ID SINTA</dt>
                            <dd class="fw-medium">
                                @if ($user->identity?->sinta_id)
                                    <a href="https://sinta.kemdiktisaintek.go.id/authors/profile/{{ $user->identity->sinta_id }}"
                                        target="_blank" rel="noopener noreferrer">{{ $user->identity->sinta_id }}</a>
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div class="col-12">
                            <dt class="text-secondary">Institusi</dt>
                            <dd class="fw-medium">{{ $user->identity?->institution?->name ?? '—' }}</dd>
                        </div>
                        <div class="col-12">
                            <dt class="text-secondary">Fakultas</dt>
                            <dd class="fw-medium">{{ $user->identity?->faculty?->name ?? '—' }} /
                                {{ $user->identity?->faculty?->code ?? '—' }}</dd>
                        </div>
                        <div class="col-12">
                            <dt class="text-secondary">Program Studi</dt>
                            <dd class="fw-medium">{{ $user->identity?->studyProgram?->name ?? '—' }}</dd>
                        </div>
                        <div class="col-12">
                            <dt class="text-secondary">Alamat</dt>
                            <dd class="fw-medium">{{ $user->identity?->address ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-3 card">
                <div class="card-body">
                    <h3 class="mb-3 card-title">Ringkasan keamanan</h3>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary">Autentikasi dua faktor</span>
                            @if ($user->two_factor_secret)
                                <x-tabler.badge color="green">Diaktifkan</x-tabler.badge>
                            @else
                                <x-tabler.badge color="secondary">Dinonaktifkan</x-tabler.badge>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary">Terakhir diperbarui</span>
                            <span class="fw-medium">{{ optional($user->updated_at)->diffForHumans() ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">


            <div class="mt-3 card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Penelitian</h3>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tahun</th>
                                <th>Peran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->researches as $research)
                                <tr>
                                    <td class="text-wrap">
                                        <div class="fw-medium">
                                            <a href="{{ route('research.proposal.show', $research) }}"
                                                class="text-reset" wire:navigate.hover>
                                                {{ $research->title }}
                                            </a>
                                        </div>
                                        <div class="text-secondary small">{{ $research->researchScheme?->name }}</div>
                                    </td>
                                    <td>{{ $research->start_year }}</td>
                                    <td>
                                        @php
                                            $roleName = str($research->user_role)->title();
                                            $badgeColor = $roleName == 'Ketua' ? 'bg-blue-lt' : 'bg-orange-lt';
                                        @endphp
                                        <span class="badge {{ $badgeColor }}">
                                            {{ $roleName }}
                                        </span>
                                    </td>
                                    <td>
                                        <x-tabler.badge :color="$research->status->color()">
                                            {{ $research->status->label() }}
                                        </x-tabler.badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center">Tidak ada riwayat penelitian</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($this->researches->hasPages())
                    <div class="d-flex align-items-center card-footer">
                        {{ $this->researches->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>

            <div class="mt-3 card">
                <div class="card-header">
                    <h3 class="card-title">Riwayat Pengabdian Masyarakat</h3>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter text-nowrap datatable">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tahun</th>
                                <th>Peran</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($this->communityServices as $pkm)
                                <tr>
                                    <td class="text-wrap">
                                        <div class="fw-medium">
                                            <a href="{{ route('community-service.proposal.show', $pkm) }}"
                                                class="text-reset" wire:navigate.hover>
                                                {{ $pkm->title }}
                                            </a>
                                        </div>
                                        <div class="text-secondary small">{{ $pkm->researchScheme?->name }}</div>
                                    </td>
                                    <td>{{ $pkm->start_year }}</td>
                                    <td>
                                        @php
                                            $roleName = str($pkm->user_role)->title();
                                            $badgeColor = $roleName == 'Ketua' ? 'bg-blue-lt' : 'bg-orange-lt';
                                        @endphp
                                        <span class="badge {{ $badgeColor }}">
                                            {{ $roleName }}
                                        </span>
                                    </td>
                                    <td>
                                        <x-tabler.badge :color="$pkm->status->color()">
                                            {{ $pkm->status->label() }}
                                        </x-tabler.badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center">Tidak ada riwayat pengabdian
                                        masyarakat</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($this->communityServices->hasPages())
                    <div class="d-flex align-items-center card-footer">
                        {{ $this->communityServices->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
