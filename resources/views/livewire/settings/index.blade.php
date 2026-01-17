<x-slot:title>Pengaturan</x-slot:title>
<x-slot:pageTitle>Pengaturan Akun</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola pengaturan akun dan preferensi Anda.</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />
    <div class="card">
        <div class="row g-0">
            <div class="border-end col-12 col-md-3">
                <div class="card-body">
                    <h4 class="subheader">Pengaturan Akun</h4>
                    <div class="list-group list-group-transparent">
                        <button wire:click="setActiveTab('profile')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'profile' ? 'active' : '' }}">
                            <x-lucide-user class="me-2 icon" />
                            Profil Saya
                        </button>
                        <button wire:click="setActiveTab('password')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'password' ? 'active' : '' }}">
                            <x-lucide-lock class="me-2 icon" />
                            Ubah Kata Sandi
                        </button>
                        {{-- <button wire:click="setActiveTab('two-factor')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'two-factor' ? 'active' : '' }}">
                            <x-lucide-shield class="me-2 icon" />
                            Autentikasi Dua Faktor
                        </button> --}}
                    </div>
                    @role('admin lppm')
                        <h4 class="mt-4 subheader">Pengaturan Sistem</h4>
                        <div class="list-group list-group-transparent">
                            <button wire:click="setActiveTab('appearance')"
                                class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'appearance' ? 'active' : '' }}">
                                <x-lucide-palette class="me-2 icon" />
                                Tampilan
                            </button>
                        </div>
                    @endrole
                </div>
            </div>

            <div class="d-flex flex-column col-12 col-md-9">
                <div class="card-body">
                    @if ($activeTab === 'profile')
                        <div>
                            <h2 class="mb-4">Profil Saya</h2>
                            <h3 class="card-title">Detail Profil</h3>
                            <div class="align-items-center mb-4 row">
                                <div class="col-auto">
                                    <span class="avatar avatar-xl"
                                        style="background-image: url({{ auth()->user()->profilePicture }})">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="col-auto">
                                    <span class="text-muted small">Avatar dikelola melalui Gravatar</span>
                                </div>
                            </div>

                            <livewire:settings.profile-form />
                        </div>
                    @elseif ($activeTab === 'password')
                        <div>
                            <h2 class="mb-4">Ubah Kata Sandi</h2>
                            <p class="mb-4 card-subtitle">Pastikan akun Anda menggunakan kata sandi yang panjang dan
                                acak untuk tetap aman.</p>
                            <livewire:settings.password />
                        </div>
                        {{-- @elseif ($activeTab === 'two-factor')
                        <div>
                            <livewire:settings.two-factor />
                        </div> --}}
                    @elseif ($activeTab === 'appearance')
                        <div>
                            <livewire:settings.appearance />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
