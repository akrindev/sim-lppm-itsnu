<div x-data="{
    config: {
        theme: 'light',
        'theme-base': 'gray',
        'theme-font': 'sans-serif',
        'theme-primary': 'blue',
        'theme-radius': '1',
    },
    defaults: {
        theme: 'light',
        'theme-base': 'gray',
        'theme-font': 'sans-serif',
        'theme-primary': 'blue',
        'theme-radius': '1',
    },
    url: new URL(window.location),

    init() {
        this.loadSettings();
    },

    loadSettings() {
        for (let key in this.defaults) {
            const savedValue = window.localStorage.getItem('tabler-' + key);
            this.config[key] = savedValue || this.defaults[key];
            this.applyTheme(key, this.config[key]);
        }
    },

    applyTheme(key, value) {
        if (value) {
            document.documentElement.setAttribute('data-bs-' + key, value);
            window.localStorage.setItem('tabler-' + key, value);
            this.url.searchParams.set(key, value);
        }
    },

    saveTheme() {
        for (let key in this.config) {
            window.localStorage.setItem('tabler-' + key, this.config[key]);
        }
        window.history.pushState({}, '', this.url);
        console.log('Preferensi tema berhasil disimpan');
    },

    resetTheme() {
        for (let key in this.defaults) {
            const defaultValue = this.defaults[key];
            this.config[key] = defaultValue;
            window.localStorage.setItem('tabler-' + key, defaultValue);
            this.applyTheme(key, defaultValue);
        }
        window.history.pushState({}, '', this.url);
        console.log('Preferensi tema direset ke default');
    }
}" x-init="init()" class="space-y-4">
    <div class="mb-4">
        <label class="form-label">Mode Tampilan</label>
        <p class="form-hint">Pilih tema yang Anda inginkan untuk antarmuka aplikasi.</p>
        <label class="form-check">
            <div class="form-selectgroup-item">
                <input type="radio" name="theme" value="light" class="form-check-input" x-model="config.theme"
                    @change="applyTheme('theme', config.theme)" />
                <div class="form-check-label">Terang</div>
            </div>
        </label>
        <label class="form-check">
            <div class="form-selectgroup-item">
                <input type="radio" name="theme" value="dark" class="form-check-input" x-model="config.theme"
                    @change="applyTheme('theme', config.theme)" />
                <div class="form-check-label">Gelap</div>
            </div>
        </label>
    </div>

    <div class="mb-4">
        <label class="form-label">Skema Warna</label>
        <p class="form-hint">Pilih warna utama yang sempurna untuk aplikasi Anda.</p>
        <div class="row g-2">
            <template
                x-for="color in ['blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime', 'green', 'teal', 'cyan']"
                :key="color">
                <div class="col-auto">
                    <label class="form-colorinput">
                        <input name="theme-primary" type="radio" :value="color" class="form-colorinput-input"
                            x-model="config['theme-primary']"
                            @change="applyTheme('theme-primary', config['theme-primary'])" />
                        <span :class="'bg-' + color + ' form-colorinput-color'"></span>
                    </label>
                </div>
            </template>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Jenis Huruf</label>
        <p class="form-hint">Pilih jenis huruf yang sesuai dengan aplikasi Anda.</p>
        <div>
            <template x-for="font in ['sans-serif', 'serif', 'monospace', 'comic']" :key="font">
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-font" :value="font" class="form-check-input"
                            x-model="config['theme-font']" @change="applyTheme('theme-font', config['theme-font'])" />
                        <div class="form-check-label" x-text="font.charAt(0).toUpperCase() + font.slice(1)"></div>
                    </div>
                </label>
            </template>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Tema Dasar</label>
        <p class="form-hint">Pilih gradasi abu-abu untuk aplikasi Anda.</p>
        <div>
            <template x-for="base in ['slate', 'gray', 'zinc', 'neutral', 'stone']" :key="base">
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-base" :value="base" class="form-check-input"
                            x-model="config['theme-base']" @change="applyTheme('theme-base', config['theme-base'])" />
                        <div class="form-check-label" x-text="base.charAt(0).toUpperCase() + base.slice(1)"></div>
                    </div>
                </label>
            </template>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label">Radius Sudut</label>
        <p class="form-hint">Pilih faktor radius border untuk aplikasi Anda.</p>
        <div>
            <template x-for="radius in ['0', '0.5', '1', '1.5', '2']" :key="radius">
                <label class="form-check">
                    <div class="form-selectgroup-item">
                        <input type="radio" name="theme-radius" :value="radius" class="form-check-input"
                            x-model="config['theme-radius']"
                            @change="applyTheme('theme-radius', config['theme-radius'])" />
                        <div class="form-check-label" x-text="radius"></div>
                    </div>
                </label>
            </template>
        </div>
    </div>

    <div class="bg-transparent mt-auto card-footer">
        <div class="justify-content-end btn-list">
            <button type="button" class="btn" @click="resetTheme()">
                <x-lucide-rotate-ccw class="icon icon-1" />
                Reset Perubahan
            </button>
            <button type="button" class="btn btn-primary" @click="saveTheme()">
                Simpan Preferensi
            </button>
        </div>
    </div>
</div>
