<div class="page page-center">
    <div class="container-xl py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                {{-- Header --}}
                <div class="text-center mb-4">
                    <a href="." class="navbar-brand navbar-brand-autodark">
                        <img src="{{ asset('logo.png') }}" alt="Logo" width="80" height="80">
                    </a>
                    <h1 class="mt-3">LPPM-ITSNU Installation</h1>
                    <p class="text-secondary">Ayo siapkan sistem manajemen penelitian Anda</p>
                </div>

                {{-- Step Indicator --}}
                <div class="card card-md mb-4">
                    <div class="card-body py-3">
                        <div class="steps steps-counter steps-primary">
                            @foreach ([
                                1 => 'Environment',
                                2 => 'Database',
                                3 => 'Institusi',
                                4 => 'Admin',
                                5 => 'Install'
                            ] as $step => $label)
                                <a href="#"
                                   wire:click.prevent="goToStep({{ $step }})"
                                   class="step-item {{ $currentStep > $step ? 'active' : '' }} {{ $currentStep === $step ? 'active' : '' }}"
                                   @if($currentStep < $step) style="pointer-events: none; opacity: 0.5;" @endif>
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Main Content Card --}}
                <div class="card card-md">
                    <div class="card-body">
                        @if ($currentStep === 1)
                            {{-- Step 1: Environment Check --}}
                            <h2 class="card-title mb-1">Pengecekan Environment</h2>
                            <p class="text-secondary mb-4">Memverifikasi server Anda memenuhi semua persyaratan...</p>

                            <div class="list-group list-group-flush mb-4">
                                @foreach ($environmentChecks as $key => $check)
                                    <div class="list-group-item d-flex align-items-center {{ $check['status'] ? '' : 'list-group-item-danger' }}">
                                        <span class="me-3">
                                            @if ($check['status'])
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                                    <path d="M9 12l2 2l4 -4"/>
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                                    <path d="M12 9v4"/>
                                                    <path d="M12 16v.01"/>
                                                </svg>
                                            @endif
                                        </span>
                                        <div class="flex-fill">
                                            <div class="fw-medium">{{ $check['label'] }}</div>
                                            @if (! $check['status'])
                                                <div class="text-danger small">Diperlukan: {{ $check['required'] }}</div>
                                            @endif
                                        </div>
                                        <div class="text-end">
                                            <x-tabler.badge :color="$check['status'] ? 'success' : 'danger'" variant="solid">
                                                {{ $check['current'] }}
                                            </x-tabler.badge>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if (! $environmentPassed)
                                <div class="alert alert-warning">
                                    <div class="d-flex">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 9v4"/>
                                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"/>
                                                <path d="M12 16h.01"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Perbaiki masalah di atas sebelum melanjutkan</h4>
                                            <div class="text-secondary">
                                                Jika izin direktori yang menjadi masalah, jalankan:
                                                <code class="bg-warning-subtle px-2 py-1 rounded">chmod -R 755 storage bootstrap/cache</code>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between">
                                <button type="button" class="btn" wire:click="checkEnvironment" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="checkEnvironment">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                        </svg>
                                        Periksa Ulang
                                    </span>
                                    <span wire:loading wire:target="checkEnvironment">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Memeriksa...
                                    </span>
                                </button>
                                @if ($environmentPassed)
                                    <button type="button" class="btn btn-primary" wire:click="nextStep">
                                        Lanjutkan
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l14 0"/>
                                            <path d="M13 18l6 -6"/>
                                            <path d="M13 6l6 6"/>
                                        </svg>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-primary" disabled>
                                        Lanjutkan
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l14 0"/>
                                            <path d="M13 18l6 -6"/>
                                            <path d="M13 6l6 6"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>

                        @elseif ($currentStep === 2)
                            {{-- Step 2: Database Configuration --}}
                            <h2 class="card-title mb-1">Konfigurasi Database</h2>
                            <p class="text-secondary mb-4">Masukkan kredensial database MariaDB/MySQL Anda</p>

                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="form-label required">Database Host</label>
                                    <input type="text" class="form-control @error('databaseForm.host') is-invalid @enderror" wire:model="databaseForm.host" placeholder="127.0.0.1">
                                    @error('databaseForm.host')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Port</label>
                                    <input type="text" class="form-control @error('databaseForm.port') is-invalid @enderror" wire:model="databaseForm.port" placeholder="3306">
                                    @error('databaseForm.port')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Nama Database</label>
                                <input type="text" class="form-control @error('databaseForm.database') is-invalid @enderror" wire:model="databaseForm.database" placeholder="lppm_itsnu">
                                @error('databaseForm.database')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label required">Username</label>
                                    <input type="text" class="form-control @error('databaseForm.username') is-invalid @enderror" wire:model="databaseForm.username" placeholder="root">
                                    @error('databaseForm.username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control @error('databaseForm.password') is-invalid @enderror" wire:model="databaseForm.password" placeholder="Biarkan kosong jika tidak ada">
                                    @error('databaseForm.password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-check">
                                    <input type="checkbox" class="form-check-input" wire:model="databaseForm.createDatabase">
                                    <span class="form-check-label">Buat database jika belum ada</span>
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button class="btn btn-ghost-secondary" wire:click="previousStep">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M5 12l6 6"/>
                                        <path d="M5 12l6 -6"/>
                                    </svg>
                                    Kembali
                                </button>
                                <div class="btn-list">
                                    <button class="btn" wire:click="testDatabaseConnection" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="testDatabaseConnection">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3.5 5.5l1.5 1.5l2.5 -2.5"/>
                                                <path d="M3.5 11.5l1.5 1.5l2.5 -2.5"/>
                                                <path d="M3.5 17.5l1.5 1.5l2.5 -2.5"/>
                                                <path d="M11 6l9 0"/>
                                                <path d="M11 12l9 0"/>
                                                <path d="M11 18l9 0"/>
                                            </svg>
                                            Test Koneksi
                                        </span>
                                        <span wire:loading wire:target="testDatabaseConnection">
                                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                            Testing...
                                        </span>
                                    </button>
                                    <button class="btn btn-primary" wire:click="nextStep">
                                        Lanjutkan
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l14 0"/>
                                            <path d="M13 18l6 -6"/>
                                            <path d="M13 6l6 6"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        @elseif ($currentStep === 3)
                            {{-- Step 3: Institution Setup --}}
                            <h2 class="card-title mb-1">Pengaturan Institusi</h2>
                            <p class="text-secondary mb-4">Konfigurasi detail institusi Anda</p>

                            <div class="mb-3">
                                <label class="form-label required">Nama Institusi</label>
                                <input type="text" class="form-control @error('institutionForm.institutionName') is-invalid @enderror" wire:model="institutionForm.institutionName" placeholder="Institut Teknologi dan Sains Nahdlatul Ulama">
                                @error('institutionForm.institutionName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Nama Singkat</label>
                                <input type="text" class="form-control @error('institutionForm.institutionShortName') is-invalid @enderror" wire:model="institutionForm.institutionShortName" placeholder="ITSNU Pekalongan">
                                @error('institutionForm.institutionShortName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" rows="2" wire:model="institutionForm.address" placeholder="Alamat lengkap institusi"></textarea>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Telepon</label>
                                    <input type="text" class="form-control" wire:model="institutionForm.phone" placeholder="(0285) 123456">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" wire:model="institutionForm.email" placeholder="lppm@itsnu.ac.id">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Website</label>
                                <input type="url" class="form-control" wire:model="institutionForm.website" placeholder="https://itsnu.ac.id">
                            </div>

                            {{-- Faculties --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">Fakultas</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="addFaculty">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 5l0 14"/>
                                            <path d="M5 12l14 0"/>
                                        </svg>
                                        Tambah Fakultas
                                    </button>
                                </div>

                                @foreach ($institutionForm->faculties as $index => $faculty)
                                    <div class="row g-2 mb-2" wire:key="faculty-{{ $index }}">
                                        <div class="col">
                                            <input type="text" class="form-control @error('institutionForm.faculties.' . $index . '.name') is-invalid @enderror" placeholder="Nama Fakultas" wire:model="institutionForm.faculties.{{ $index }}.name">
                                            @error('institutionForm.faculties.' . $index . '.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-auto" style="width: 120px;">
                                            <input type="text" class="form-control" placeholder="Kode" wire:model="institutionForm.faculties.{{ $index }}.code">
                                        </div>
                                        @if (count($institutionForm->faculties) > 1)
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-ghost-danger btn-icon" wire:click="removeFaculty({{ $index }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M4 7l16 0"/>
                                                        <path d="M10 11l0 6"/>
                                                        <path d="M14 11l0 6"/>
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-between">
                                <button class="btn btn-ghost-secondary" wire:click="previousStep">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M5 12l6 6"/>
                                        <path d="M5 12l6 -6"/>
                                    </svg>
                                    Kembali
                                </button>
                                <button class="btn btn-primary" wire:click="nextStep">
                                    Lanjutkan
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M13 18l6 -6"/>
                                        <path d="M13 6l6 6"/>
                                    </svg>
                                </button>
                            </div>

                        @elseif ($currentStep === 4)
                            {{-- Step 4: Admin Account --}}
                            <h2 class="card-title mb-1">Akun Administrator</h2>
                            <p class="text-secondary mb-4">Buat akun administrator utama</p>

                            <div class="mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" class="form-control @error('adminForm.name') is-invalid @enderror" wire:model="adminForm.name" placeholder="Administrator">
                                @error('adminForm.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Alamat Email</label>
                                <input type="email" class="form-control @error('adminForm.email') is-invalid @enderror" wire:model="adminForm.email" placeholder="admin@example.com">
                                @error('adminForm.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Password</label>
                                <input type="password" class="form-control @error('adminForm.password') is-invalid @enderror" wire:model="adminForm.password">
                                @error('adminForm.password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-hint">Minimal 8 karakter dengan huruf besar, huruf kecil, dan angka</div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label required">Konfirmasi Password</label>
                                <input type="password" class="form-control @error('adminForm.passwordConfirmation') is-invalid @enderror" wire:model="adminForm.passwordConfirmation">
                                @error('adminForm.passwordConfirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <button class="btn btn-ghost-secondary" wire:click="previousStep">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M5 12l6 6"/>
                                        <path d="M5 12l6 -6"/>
                                    </svg>
                                    Kembali
                                </button>
                                <button class="btn btn-primary" wire:click="nextStep">
                                    Lanjutkan
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M5 12l14 0"/>
                                        <path d="M13 18l6 -6"/>
                                        <path d="M13 6l6 6"/>
                                    </svg>
                                </button>
                            </div>

                        @elseif ($currentStep === 5)
                            {{-- Step 5: Installation Progress --}}
                            <h2 class="card-title mb-1">Instalasi</h2>
                            <p class="text-secondary mb-4">Menyiapkan aplikasi Anda...</p>

                            @if (! $installationProgress['complete'] && ! $isInstalling)
                                <div class="alert alert-info">
                                    <div class="d-flex">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                                <path d="M12 9h.01"/>
                                                <path d="M11 12h1v4h1"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Siap untuk Instalasi</h4>
                                            <div class="text-secondary mb-3">Semua konfigurasi telah selesai. Klik tombol di bawah untuk memulai proses instalasi.</div>
                                            <div class="row g-2 text-secondary">
                                                <div class="col-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 12l5 5l10 -10"/>
                                                    </svg>
                                                    Environment OK
                                                </div>
                                                <div class="col-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 12l5 5l10 -10"/>
                                                    </svg>
                                                    Database OK
                                                </div>
                                                <div class="col-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 12l5 5l10 -10"/>
                                                    </svg>
                                                    Institusi OK
                                                </div>
                                                <div class="col-6">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-success me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M5 12l5 5l10 -10"/>
                                                    </svg>
                                                    Admin OK
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="btn btn-primary w-100" wire:click="startInstallation">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"/>
                                        <path d="M7 11l5 5l5 -5"/>
                                        <path d="M12 4l0 12"/>
                                    </svg>
                                    Mulai Instalasi
                                </button>
                            @endif

                            @if ($isInstalling || $installationProgress['complete'])
                                {{-- Progress Bar --}}
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-medium">{{ $installationProgress['message'] }}</span>
                                        <span class="text-secondary">{{ $installationProgress['percent'] }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar {{ $installationProgress['error'] ? 'bg-danger' : ($installationProgress['complete'] ? 'bg-success' : 'progress-bar-indeterminate') }}" role="progressbar" style="width: {{ $installationProgress['percent'] }}%"></div>
                                    </div>
                                </div>

                                @if ($installationProgress['error'])
                                    <div class="alert alert-danger">
                                        <div class="d-flex">
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"/>
                                                    <path d="M12 8v4"/>
                                                    <path d="M12 16h.01"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="alert-title">Instalasi Gagal</h4>
                                                <div class="text-secondary">{{ $installationProgress['error'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Installation Logs --}}
                                @if (count($installationProgress['logs']) > 0)
                                    <div class="mb-3">
                                        <label class="form-label">Log Instalasi</label>
                                        <div class="bg-dark text-light rounded p-3" style="max-height: 200px; overflow-y: auto; font-family: monospace; font-size: 12px;">
                                            @foreach ($installationProgress['logs'] as $log)
                                                <div class="text-success-lt">{{ $log }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endif

                            @if ($installationProgress['complete'])
                                <div class="alert alert-success">
                                    <div class="d-flex">
                                        <div>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                                                <path d="M9 12l2 2l4 -4"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="alert-title">Instalasi Selesai!</h4>
                                            <div class="text-secondary">Sistem LPPM-ITSNU Anda sekarang siap digunakan.</div>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('login') }}" class="btn btn-success w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M9 8v-2a2 2 0 0 1 2 -2h7a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-7a2 2 0 0 1 -2 -2v-2"/>
                                        <path d="M3 12h13l-3 -3"/>
                                        <path d="M13 15l3 -3"/>
                                    </svg>
                                    Ke Halaman Login
                                </a>
                            @endif

                            @if (! $installationProgress['complete'])
                                <div class="d-flex justify-content-between mt-4">
                                    <button class="btn btn-ghost-secondary" wire:click="previousStep" @disabled($isInstalling)>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M5 12l14 0"/>
                                            <path d="M5 12l6 6"/>
                                            <path d="M5 12l6 -6"/>
                                        </svg>
                                        Kembali
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Footer --}}
                <div class="text-center text-secondary mt-4">
                    <small>{{ config('app.name') }} &copy; {{ date('Y') }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"
         x-data="{ show: false, message: '', type: 'success' }"
         x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition
         x-cloak>
        <div class="toast show" role="alert">
            <div class="toast-header">
                <span class="avatar avatar-xs me-2" :class="type === 'success' ? 'bg-success' : 'bg-danger'">
                    <template x-if="type === 'success'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M5 12l5 5l10 -10"/>
                        </svg>
                    </template>
                    <template x-if="type !== 'success'">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M18 6l-12 12"/>
                            <path d="M6 6l12 12"/>
                        </svg>
                    </template>
                </span>
                <strong class="me-auto" x-text="type === 'success' ? 'Berhasil' : 'Gagal'"></strong>
                <button type="button" class="ms-2 btn-close" @click="show = false"></button>
            </div>
            <div class="toast-body" x-text="message"></div>
        </div>
    </div>
</div>
