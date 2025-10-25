<div>
    <x-slot:pageActions>
        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
            {{ __('Ubah pengguna') }}
        </a>
    </x-slot:pageActions>

    <div class="row row-cards">
        <div class="col-md-5">
            <div class="h-100 card">
                <div class="text-center card-body">
                    <span class="mb-3 avatar avatar-xl" style="background-image: url('{{ $user->profilePicture }}')"></span>
                    <h2 class="mb-1">{{ $user->name }}</h2>
                    <p class="text-secondary">{{ $user->email }}</p>
                    <div class="my-3">
                        @if ($user->roles->isNotEmpty())
                            <span class="bg-primary-lt text-primary badge badge-lg">{{ str($user->roles->first()->name)->title() }}</span>
                        @else
                            <span class="text-secondary">{{ __('Tidak ada peran') }}</span>
                        @endif
                    </div>
                    <div class="mt-3">
                        @if ($user->hasVerifiedEmail())
                            <span class="bg-green-lt text-green badge">{{ __('Email terverifikasi') }}</span>
                        @else
                            <span class="bg-yellow-lt text-yellow badge">{{ __('Verifikasi menunggu') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">{{ __('Detail profil') }}</h3>
                    <dl class="row row-cards">
                        <div class="col-sm-6">
                            <dt class="text-secondary">{{ __('ID Identitas') }}</dt>
                            <dd class="fw-medium">{{ $user->identity?->identity_id ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">{{ __('Bergabung platform') }}</dt>
                            <dd class="fw-medium">{{ optional($user->created_at)->translatedFormat('d F Y') ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">{{ __('Tempat Lahir') }}</dt>
                            <dd class="fw-medium">{{ $user->identity?->birthplace ?? '—' }}</dd>
                        </div>
                        <div class="col-sm-6">
                            <dt class="text-secondary">{{ __('Tanggal Lahir') }}</dt>
                            <dd class="fw-medium">
                                {{ $user->identity?->birthdate ? \Illuminate\Support\Carbon::parse($user->identity->birthdate)->translatedFormat('d F Y') : '—' }}
                            </dd>
                        </div>
                        <div class="col-12">
                            <dt class="text-secondary">{{ __('Alamat') }}</dt>
                            <dd class="fw-medium">{{ $user->identity?->address ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="mt-3 card">
                <div class="card-body">
                    <h3 class="mb-3 card-title">{{ __('Ringkasan keamanan') }}</h3>
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary">{{ __('Autentikasi dua faktor') }}</span>
                            @if ($user->two_factor_secret)
                                <span class="bg-green-lt text-green badge">{{ __('Diaktifkan') }}</span>
                            @else
                                <span class="bg-secondary badge">{{ __('Dinonaktifkan') }}</span>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="text-secondary">{{ __('Terakhir diperbarui') }}</span>
                            <span class="fw-medium">{{ optional($user->updated_at)->diffForHumans() ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
