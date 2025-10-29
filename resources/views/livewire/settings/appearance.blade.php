<div>
    <h3 class="card-title mt-4">Mode Tampilan</h3>
    <p class="card-subtitle">Pilih tema yang Anda inginkan untuk antarmuka aplikasi.</p>

    <div class="row g-3">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-none">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            <x-lucide-sun class="icon" />
                        </div>
                        <div>
                            <div class="fw-medium">Terang</div>
                            <div class="text-muted small">Tema terang standar</div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="theme" value="light" checked />
                        <label class="form-check-label">Pilih Tema Terang</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-none">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            <x-lucide-moon class="icon" />
                        </div>
                        <div>
                            <div class="fw-medium">Gelap</div>
                            <div class="text-muted small">Tema gelap nyaman mata</div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="theme" value="dark" />
                        <label class="form-check-label">Pilih Tema Gelap</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-none">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-2">
                            <x-lucide-monitor class="icon" />
                        </div>
                        <div>
                            <div class="fw-medium">Sistem</div>
                            <div class="text-muted small">Ikuti pengaturan sistem</div>
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="theme" value="system" />
                        <label class="form-check-label">Ikuti Sistem</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h3 class="card-title mt-4">Preferensi Bahasa</h3>
    <p class="card-subtitle">Pilih bahasa yang akan digunakan untuk antarmuka aplikasi.</p>

    <div class="mb-3">
        <div class="form-label">Bahasa</div>
        <select class="form-select" aria-label="Pilih bahasa">
            <option value="id" selected>Bahasa Indonesia</option>
            <option value="en">English</option>
        </select>
    </div>

    <h3 class="card-title mt-4">Preferensi Notifikasi</h3>
    <p class="card-subtitle">Kelola bagaimana Anda ingin menerima notifikasi.</p>

    <div class="form-check form-switch mb-2">
        <input class="form-check-input" type="checkbox" id="notifications-enabled" checked />
        <label class="form-check-label" for="notifications-enabled">
            Aktifkan notifikasi push
        </label>
        <div class="text-muted small ms-4">Terima notifikasi di browser</div>
    </div>

    <div class="form-check form-switch mb-2">
        <input class="form-check-input" type="checkbox" id="email-notifications" checked />
        <label class="form-check-label" for="email-notifications">
            Notifikasi email
        </label>
        <div class="text-muted small ms-4">Terima email untuk update penting</div>
    </div>

    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="marketing-emails" />
        <label class="form-check-label" for="marketing-emails">
            Email pemasaran
        </label>
        <div class="text-muted small ms-4">Terima email tentang fitur baru dan pembaruan</div>
    </div>

    <div class="card-footer bg-transparent mt-auto">
        <div class="btn-list justify-content-end">
            <button type="button" class="btn btn-primary">
                Simpan Preferensi
            </button>
        </div>
    </div>
</div>
