<x-layouts.auth>
    <div class="py-4 container-tight">
        <div class="mb-4 text-center">
            <a href="." class="navbar-brand navbar-brand-autodark">
                <img src="/logo.png" alt="Logo" width="100" height="100">
            </a>
        </div>
        <div class="card card-md">
            <div class="card-body">
                <div
                    x-cloak
                    x-data="{
                        showRecoveryInput: @js($errors->has('recovery_code')),
                        code: '',
                        recovery_code: '',
                        toggleInput() {
                            this.showRecoveryInput = !this.showRecoveryInput;
                            this.code = '';
                            this.recovery_code = '';
                            $dispatch('clear-2fa-auth-code');
                            $nextTick(() => {
                                this.showRecoveryInput
                                    ? this.$refs.recovery_code?.focus()
                                    : $dispatch('focus-2fa-auth-code');
                            });
                        },
                    }"
                >
                    <div x-show="!showRecoveryInput">
                        <h2 class="mb-4 text-center h2">{{ __('Kode Autentikasi') }}</h2>
                        <p class="mb-4 text-secondary text-center">{{ __('Masukkan kode autentikasi yang disediakan oleh aplikasi autentikator Anda.') }}</p>
                    </div>

                    <div x-show="showRecoveryInput">
                        <h2 class="mb-4 text-center h2">{{ __('Kode Pemulihan') }}</h2>
                        <p class="mb-4 text-secondary text-center">{{ __('Silakan konfirmasi akses ke akun Anda dengan memasukkan salah satu kode pemulihan darurat Anda.') }}</p>
                    </div>

                    <form method="POST" action="{{ route('two-factor.login.store') }}">
                        @csrf

                        <div x-show="!showRecoveryInput">
                            <div class="d-flex justify-content-center mb-3">
                                <x-input-otp
                                    name="code"
                                    digits="6"
                                    autocomplete="one-time-code"
                                    x-model="code"
                                />
                            </div>

                            @error('code')
                                <div class="d-block mb-3 text-center invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div x-show="showRecoveryInput">
                            <div class="mb-3">
                                <input
                                    type="text"
                                    name="recovery_code"
                                    class="form-control"
                                    x-ref="recovery_code"
                                    x-bind:required="showRecoveryInput"
                                    autocomplete="one-time-code"
                                    x-model="recovery_code"
                                />
                            </div>

                            @error('recovery_code')
                                <div class="d-block mb-3 invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-footer">
                            <button type="submit" class="w-100 btn btn-primary">
                                {{ __('Lanjutkan') }}
                            </button>
                        </div>

                        <div class="mt-3 text-secondary text-center">
                            <span>{{ __('atau Anda dapat') }}</span>
                            <div class="d-inline">
                                <a href="#" class="link-primary" x-show="!showRecoveryInput" @click.prevent="toggleInput()">{{ __('masuk menggunakan kode pemulihan') }}</a>
                                <a href="#" class="link-primary" x-show="showRecoveryInput" @click.prevent="toggleInput()">{{ __('masuk menggunakan kode autentikasi') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.auth>
