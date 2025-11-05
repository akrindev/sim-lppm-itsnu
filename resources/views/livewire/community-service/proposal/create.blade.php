<x-slot:title>Usulan Penelitian Baru</x-slot:title>
<x-slot:pageTitle>Usulan Penelitian Baru</x-slot:pageTitle>
<x-slot:pageSubtitle>Buat proposal penelitian baru dengan mengisi form di bawah ini.</x-slot:pageSubtitle>
<x-slot:pageActions>
    <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn">
        <x-lucide-arrow-left class="icon" />
        Kembali ke Daftar
    </a>
</x-slot:pageActions>

<div>
    <x-tabler.alert />

    <!-- Step Indicator -->
    <div class="mb-3 card">
        <div class="card-body">
            <ul class="my-4 steps steps-green steps-counter">
                <li class="step-item {{ $currentStep === 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}">
                    Identitas Usulan
                </li>
                <li class="step-item {{ $currentStep === 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}">
                    Substansi Usulan
                </li>
                <li class="step-item {{ $currentStep === 3 ? 'active' : '' }} {{ $currentStep > 3 ? 'completed' : '' }}">
                    RAB
                </li>
                <li class="step-item {{ $currentStep === 4 ? 'active' : '' }} {{ $currentStep > 4 ? 'completed' : '' }}">
                    Dokumen Pendukung
                </li>
                <li class="step-item {{ $currentStep === 5 ? 'active' : '' }}">
                    Konfirmasi Usulan
                </li>
            </ul>
        </div>
    </div>

    <form wire:submit.prevent="save" novalidate>
        <!-- Step Content -->
        @if ($currentStep === 1)
            @include('livewire.community-service.proposal.steps.identitas-usulan')
        @elseif ($currentStep === 2)
            @include('livewire.community-service.proposal.steps.substansi-usulan')
        @elseif ($currentStep === 3)
            @include('livewire.community-service.proposal.steps.rab')
        @elseif ($currentStep === 4)
            @include('livewire.community-service.proposal.steps.dokumen-pendukung')
        @elseif ($currentStep === 5)
            @include('livewire.community-service.proposal.steps.konfirmasi')
        @endif

        <!-- Navigation Buttons -->
        <div class="d-flex justify-content-between gap-2 mt-3">
            <div>
                @if ($currentStep > 1)
                    <button type="button" wire:click="previousStep" class="btn-outline-secondary btn">
                        <x-lucide-arrow-left class="icon" />
                        Sebelumnya
                    </button>
                @else
                    <a href="{{ route('research.proposal.index') }}" class="btn-outline-secondary btn">
                        <x-lucide-x class="icon" />
                        Batal
                    </a>
                @endif
            </div>

            <div>
                @if ($currentStep < 5)
                    <button type="button" wire:click="nextStep" class="btn btn-primary">
                        Selanjutnya
                        <x-lucide-arrow-right class="icon" />
                    </button>
                @else
                    <button type="submit" class="btn btn-success">
                        <span class="me-2 spinner-border spinner-border-sm" wire:loading role="status"
                            aria-hidden="true"></span>
                        <x-lucide-save class="icon" />
                        <span wire:loading.remove>Simpan Proposal</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                @endif
            </div>
        </div>
    </form>
</div>
