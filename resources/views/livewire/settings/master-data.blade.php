<x-slot:title>Pengaturan</x-slot:title>
<x-slot:pageTitle>Master Data</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola semua data master sistem</x-slot:pageSubtitle>

<div class="page-wrapper">
    <div class="container-xl">
        <div class="mb-3 row">
            <div class="col-12">
                <!-- Tab Navigation -->
                <ul class="mb-4 nav nav-bordered" wire:key="tabs-navigation">
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'focus-areas' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('focus-areas')" href="#">
                            Area Fokus
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'keywords' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('keywords')" href="#">
                            Kata Kunci
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'national-priorities' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('national-priorities')" href="#">
                            Prioritas Nasional
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'partners' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('partners')" href="#">
                            Mitra
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'research-schemes' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('research-schemes')" href="#">
                            Skema Penelitian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'science-clusters' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('science-clusters')" href="#">
                            Klaster Sains
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'study-programs' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('study-programs')" href="#">
                            Program Studi
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'themes' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('themes')" href="#">
                            Tema
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'topics' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('topics')" href="#">
                            Topik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $activeTab === 'institutions' ? 'active' : '' }}"
                            wire:click.prevent="setActiveTab('institutions')" href="#">
                            Institusi
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Focus Areas Tab -->
        @if ($activeTab === 'focus-areas')
            <div class="card" wire:key="focus-areas">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Area Fokus</h3>
                    <button wire:click="createFocusArea" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Area Fokus
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($focusAreas as $item)
                                <tr wire:key="focus-area-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editFocusArea({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('focus-area', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $focusAreas->links() }}
                </div>
            </div>
        @endif

        <!-- Keywords Tab -->
        @if ($activeTab === 'keywords')
            <div class="card" wire:key="keywords">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Kata Kunci</h3>
                    <button wire:click="createKeyword" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Kata Kunci
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($keywords as $item)
                                <tr wire:key="keyword-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editKeyword({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('keyword', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $keywords->links() }}
                </div>
            </div>
        @endif

        <!-- National Priorities Tab -->
        @if ($activeTab === 'national-priorities')
            <div class="card" wire:key="national-priorities">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Prioritas Nasional</h3>
                    <button wire:click="createNationalPriority" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Prioritas Nasional
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($nationalPriorities as $item)
                                <tr wire:key="national-priority-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editNationalPriority({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('national-priority', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $nationalPriorities->links() }}
                </div>
            </div>
        @endif

        <!-- Partners Tab -->
        @if ($activeTab === 'partners')
            <div class="card" wire:key="partners">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Mitra</h3>
                    <button wire:click="createPartner" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Mitra
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tipe</th>
                                <th>Alamat</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($partners as $item)
                                <tr wire:key="partner-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        @if ($item->type)
                                            <x-tabler.badge color="blue">{{ $item->type }}</x-tabler.badge>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($item->address, 50) ?? '-' }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editPartner({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('partner', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $partners->links() }}
                </div>
            </div>
        @endif

        <!-- Research Schemes Tab -->
        @if ($activeTab === 'research-schemes')
            <div class="card" wire:key="research-schemes">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Skema Penelitian</h3>
                    <button wire:click="createResearchScheme" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Skema Penelitian
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Strata</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($researchSchemes as $item)
                                <tr wire:key="research-scheme-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <x-tabler.badge color="green">{{ $item->strata }}</x-tabler.badge>
                                    </td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editResearchScheme({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('research-scheme', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $researchSchemes->links() }}
                </div>
            </div>
        @endif

        <!-- Science Clusters Tab -->
        @if ($activeTab === 'science-clusters')
            <div class="card" wire:key="science-clusters">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Klaster Sains</h3>
                    <button wire:click="createScienceCluster" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Klaster Sains
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($scienceClusters as $item)
                                <tr wire:key="science-cluster-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editScienceCluster({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('science-cluster', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $scienceClusters->links() }}
                </div>
            </div>
        @endif

        <!-- Study Programs Tab -->
        @if ($activeTab === 'study-programs')
            <div class="card" wire:key="study-programs">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Program Studi</h3>
                    <button wire:click="createStudyProgram" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Program Studi
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Institusi</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studyPrograms as $item)
                                <tr wire:key="study-program-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->institution->name ?? '-' }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editStudyProgram({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('study-program', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $studyPrograms->links() }}
                </div>
            </div>
        @endif

        <!-- Themes Tab -->
        @if ($activeTab === 'themes')
            <div class="card" wire:key="themes">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Tema</h3>
                    <button wire:click="createTheme" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Tema
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Area Fokus</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($themes as $item)
                                <tr wire:key="theme-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->focusArea->name ?? '-' }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editTheme({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('theme', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $themes->links() }}
                </div>
            </div>
        @endif

        <!-- Topics Tab -->
        @if ($activeTab === 'topics')
            <div class="card" wire:key="topics">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Topik</h3>
                    <button wire:click="createTopic" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Topik
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Tema</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($topics as $item)
                                <tr wire:key="topic-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->theme->name ?? '-' }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editTopic({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('topic', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $topics->links() }}
                </div>
            </div>
        @endif

        <!-- Institutions Tab -->
        @if ($activeTab === 'institutions')
            <div class="card" wire:key="institutions">
                <div class="d-flex align-items-center justify-content-between card-header">
                    <h3 class="card-title">Institusi</h3>
                    <button wire:click="createInstitution" class="btn btn-primary">
                        <x-lucide-plus class="icon" />
                        Tambah Institusi
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="card-table table table-vcenter">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th class="w-25">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($institutions as $item)
                                <tr wire:key="institution-{{ $item->id }}">
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <div class="flex-nowrap btn-list">
                                            <button wire:click="editInstitution({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button wire:click="confirmDelete('institution', {{ $item->id }}, '{{ $item->name }}')"
                                                class="btn-outline-danger btn btn-sm">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center card-footer">
                    {{ $institutions->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal Form -->
@if ($showModal)
    <div class="modal modal-blur fade show" id="formModal" style="display: block;" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="closeModal()" aria-label="Close"></button>
                <div class="modal-header">
                    <h5 class="modal-title">{{ $modalTitle }}</h5>
                </div>
                <form wire:submit="save">
                    <div class="modal-body">
                        {{-- Focus Area Form --}}
                        @if ($modalFor === 'focus-area')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="focusAreaName" class="form-control"
                                    placeholder="Masukkan nama area fokus">
                                @error('focusAreaName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Keyword Form --}}
                        @if ($modalFor === 'keyword')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="keywordName" class="form-control"
                                    placeholder="Masukkan kata kunci">
                                @error('keywordName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- National Priority Form --}}
                        @if ($modalFor === 'national-priority')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="nationalPriorityName" class="form-control"
                                    placeholder="Masukkan prioritas nasional">
                                @error('nationalPriorityName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Partner Form --}}
                        @if ($modalFor === 'partner')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="partnerName" class="form-control"
                                    placeholder="Masukkan nama mitra">
                                @error('partnerName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipe</label>
                                <input type="text" wire:model="partnerType" class="form-control"
                                    placeholder="Masukkan tipe mitra">
                                @error('partnerType')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea wire:model="partnerAddress" class="form-control" rows="3" placeholder="Masukkan alamat mitra"></textarea>
                                @error('partnerAddress')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Research Scheme Form --}}
                        @if ($modalFor === 'research-scheme')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="researchSchemeName" class="form-control"
                                    placeholder="Masukkan nama skema penelitian">
                                @error('researchSchemeName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Strata</label>
                                <select wire:model="researchSchemeStrata" class="form-control">
                                    <option value="">Pilih Strata</option>
                                    <option value="s1">S1</option>
                                    <option value="s2">S2</option>
                                    <option value="s3">S3</option>
                                </select>
                                @error('researchSchemeStrata')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Science Cluster Form --}}
                        @if ($modalFor === 'science-cluster')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="scienceClusterName" class="form-control"
                                    placeholder="Masukkan nama klaster sains">
                                @error('scienceClusterName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parent Klaster (Opsional)</label>
                                <select wire:model="scienceClusterParentId" class="form-control">
                                    <option value="">Tidak ada parent</option>
                                    @foreach ($scienceClusters as $cluster)
                                        <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                                    @endforeach
                                </select>
                                @error('scienceClusterParentId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Study Program Form --}}
                        @if ($modalFor === 'study-program')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="studyProgramName" class="form-control"
                                    placeholder="Masukkan nama program studi">
                                @error('studyProgramName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Institusi</label>
                                <select wire:model="institutionId" class="form-control">
                                    <option value="">Pilih Institusi</option>
                                    @foreach ($allInstitutions as $institution)
                                        <option value="{{ $institution->id }}">{{ $institution->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('institutionId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Theme Form --}}
                        @if ($modalFor === 'theme')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="themeName" class="form-control"
                                    placeholder="Masukkan nama tema">
                                @error('themeName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Area Fokus</label>
                                <select wire:model="themesFocusAreaId" class="form-control">
                                    <option value="">Pilih Area Fokus</option>
                                    @foreach ($allFocusAreas as $focusArea)
                                        <option value="{{ $focusArea->id }}">{{ $focusArea->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('themesFocusAreaId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Topic Form --}}
                        @if ($modalFor === 'topic')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="topicName" class="form-control"
                                    placeholder="Masukkan nama topik">
                                @error('topicName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tema</label>
                                <select wire:model="topicsThemeId" class="form-control">
                                    <option value="">Pilih Tema</option>
                                    @foreach ($allThemes as $theme)
                                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                                @error('topicsThemeId')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        {{-- Institution Form --}}
                        @if ($modalFor === 'institution')
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" wire:model="institutionName" class="form-control"
                                    placeholder="Masukkan nama institusi">
                                @error('institutionName')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" wire:click="closeModal()"></div>
@endif

<!-- Delete Confirmation Modal -->
@if ($showDeleteModal)
    <div class="modal modal-blur fade show" id="deleteModal" style="display: block;" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <button type="button" class="btn-close" wire:click="closeDeleteModal()" aria-label="Close"></button>
                <div class="modal-status bg-danger"></div>
                <div class="modal-body text-center py-4">
                    <x-lucide-alert-triangle class="mb-2 icon icon-lg text-danger" />
                    <h3>{{ $deleteModalTitle }}</h3>
                    <div class="text-secondary">{{ $deleteModalMessage }}</div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="w-100 btn" wire:click="closeDeleteModal()">
                                    Batal
                                </button>
                            </div>
                            <div class="col">
                                <button type="button" class="w-100 btn btn-danger" wire:click="confirmDeleteAction()">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" wire:click="closeDeleteModal()"></div>
@endif
