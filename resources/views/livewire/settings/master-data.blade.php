<x-slot:title>Pengaturan</x-slot:title>
<x-slot:pageTitle>Master Data</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola semua data master sistem</x-slot:pageSubtitle>

<div>
    <x-tabler.alert />
    <div class="card">
        <div class="row g-0">
            <div class="border-end col-12 col-md-3">
                <div class="card-body">
                    <h4 class="subheader">Konten Penelitian & Akademik</h4>
                    <div class="list-group list-group-transparent">
                        <button wire:click="setActiveTab('focus-areas')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'focus-areas' ? 'active' : '' }}">
                            <x-lucide-target class="icon me-2" />
                            Area Fokus
                        </button>
                        <button wire:click="setActiveTab('keywords')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'keywords' ? 'active' : '' }}">
                            <x-lucide-hash class="icon me-2" />
                            Kata Kunci
                        </button>
                        <button wire:click="setActiveTab('themes')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'themes' ? 'active' : '' }}">
                            <x-lucide-palette class="icon me-2" />
                            Tema
                        </button>
                        <button wire:click="setActiveTab('topics')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'topics' ? 'active' : '' }}">
                            <x-lucide-message-square class="icon me-2" />
                            Topik
                        </button>
                        <button wire:click="setActiveTab('research-schemes')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'research-schemes' ? 'active' : '' }}">
                            <x-lucide-file-text class="icon me-2" />
                            Skema Penelitian
                        </button>
                        <button wire:click="setActiveTab('science-clusters')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'science-clusters' ? 'active' : '' }}">
                            <x-lucide-atom class="icon me-2" />
                            Klaster Sains
                        </button>
                    </div>
                    <h4 class="subheader mt-4">Struktur Akademik</h4>
                    <div class="list-group list-group-transparent">
                        <button wire:click="setActiveTab('study-programs')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'study-programs' ? 'active' : '' }}">
                            <x-lucide-graduation-cap class="icon me-2" />
                            Program Studi
                        </button>
                        <button wire:click="setActiveTab('faculties')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'faculties' ? 'active' : '' }}">
                            <x-lucide-building class="icon me-2" />
                            Fakultas
                        </button>
                        <button wire:click="setActiveTab('institutions')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'institutions' ? 'active' : '' }}">
                            <x-lucide-building-2 class="icon me-2" />
                            Institusi
                        </button>
                    </div>
                    <h4 class="subheader mt-4">Anggaran & RAB</h4>
                    <div class="list-group list-group-transparent">
                        <button wire:click="setActiveTab('budget-groups')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'budget-groups' ? 'active' : '' }}">
                            <x-lucide-archive class="icon me-2" />
                            Kelompok Anggaran
                        </button>
                        <button wire:click="setActiveTab('budget-components')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'budget-components' ? 'active' : '' }}">
                            <x-lucide-package class="icon me-2" />
                            Komponen Anggaran
                        </button>
                        <button wire:click="setActiveTab('budget-caps')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'budget-caps' ? 'active' : '' }}">
                            <x-lucide-shield-check class="icon me-2" />
                            Batas Anggaran Tahunan
                        </button>
                    </div>
                    <h4 class="subheader mt-4">Kemitraan & Prioritas</h4>
                    <div class="list-group list-group-transparent">
                        <button wire:click="setActiveTab('partners')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'partners' ? 'active' : '' }}">
                            <x-lucide-users class="icon me-2" />
                            Mitra
                        </button>
                        <button wire:click="setActiveTab('national-priorities')"
                            class="list-group-item list-group-item-action d-flex align-items-center {{ $activeTab === 'national-priorities' ? 'active' : '' }}">
                            <x-lucide-star class="icon me-2" />
                            Prioritas Nasional
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column col-12 col-md-9">
                <div class="alert alert-info m-3" role="alert">
                    <div class="alert-icon">

                        <!-- Download SVG icon from http://tabler.io/icons/icon/info-circle -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon alert-icon icon-2">
                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                            <path d="M12 9h.01"></path>
                            <path d="M11 12h1v4h1"></path>
                        </svg>
                    </div>
                    Tidak Disarankan menghapus data master yang sudah digunakan pada entitas lain untuk menghindari
                    inkonsistensi data.
                </div>
                <div class="card-body">
                    @if ($activeTab === 'focus-areas')
                        <div>
                            <livewire:settings.tabs.focus-area-manager />
                        </div>
                    @elseif ($activeTab === 'keywords')
                        <div>
                            <livewire:settings.tabs.keyword-manager />
                        </div>
                    @elseif ($activeTab === 'themes')
                        <div>
                            <livewire:settings.tabs.theme-manager />
                        </div>
                    @elseif ($activeTab === 'topics')
                        <div>
                            <livewire:settings.tabs.topic-manager />
                        </div>
                    @elseif ($activeTab === 'research-schemes')
                        <div>
                            <livewire:settings.tabs.research-scheme-manager />
                        </div>
                    @elseif ($activeTab === 'science-clusters')
                        <div>
                            <livewire:settings.tabs.science-cluster-manager />
                        </div>
                    @elseif ($activeTab === 'study-programs')
                        <div>
                            <livewire:settings.tabs.study-program-manager />
                        </div>
                    @elseif ($activeTab === 'faculties')
                        <div>
                            <livewire:settings.tabs.faculty-manager />
                        </div>
                    @elseif ($activeTab === 'institutions')
                        <div>
                            <livewire:settings.tabs.institution-manager />
                        </div>
                    @elseif ($activeTab === 'partners')
                        <div>
                            <livewire:settings.tabs.partner-manager />
                        </div>
                    @elseif ($activeTab === 'national-priorities')
                        <div>
                            <livewire:settings.tabs.national-priority-manager />
                        </div>
                    @elseif ($activeTab === 'budget-groups')
                        <div>
                            <livewire:settings.tabs.budget-group-manager />
                        </div>
                    @elseif ($activeTab === 'budget-components')
                        <div>
                            <livewire:settings.tabs.budget-component-manager />
                        </div>
                    @elseif ($activeTab === 'budget-caps')
                        <div>
                            <livewire:settings.tabs.budget-cap-manager />
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
