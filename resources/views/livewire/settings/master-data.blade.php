<x-slot:title>Pengaturan</x-slot:title>
<x-slot:pageTitle>Master Data</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola semua data master sistem</x-slot:pageSubtitle>

<div>
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-focus-area" wire:click="createFocusArea">
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
                                            <button type="button" wire:click="editFocusArea({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('focus-area', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-keyword" wire:click="createKeyword">
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
                                            <button type="button" wire:click="editKeyword({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('keyword', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" data-bs-toggle="modal" data-bs-target="#modal-national-priority" wire:click="createNationalPriority" class="btn btn-primary">
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
                                            <button type="button" wire:click="editNationalPriority({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('national-priority', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-partner" wire:click="createPartner">
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
                                            <button type="button" wire:click="editPartner({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('partner', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-research-scheme" wire:click="createResearchScheme">
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
                                            <button type="button" wire:click="editResearchScheme({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('research-scheme', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-science-cluster" wire:click="createScienceCluster">
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
                                            <button type="button" wire:click="editScienceCluster({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('science-cluster', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-study-program" wire:click="createStudyProgram">
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
                                            <button type="button" wire:click="editStudyProgram({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('study-program', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-theme" wire:click="createTheme">
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
                                            <button type="button" wire:click="editTheme({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('theme', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-topic" wire:click="createTopic">
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
                                            <button type="button" wire:click="editTopic({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('topic', {{ $item->id }}, '{{ $item->name }}')"
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-institution" wire:click="createInstitution">
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
                                            <button type="button" wire:click="editInstitution({{ $item->id }})"
                                                class="btn-outline-warning btn btn-sm">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="confirmDelete('institution', {{ $item->id }}, '{{ $item->name }}')"
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

@teleport('body')
    <!-- Focus Area Modal -->
    <x-tabler.modal id="modal-focus-area" :title="$editingId ? 'Edit Area Fokus' : 'Tambah Area Fokus'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-focus-area">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="focusAreaName" class="form-control"
                        placeholder="Masukkan nama area fokus">
                    @error('focusAreaName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-focus-area" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Keyword Modal -->
    <x-tabler.modal id="modal-keyword" :title="$editingId ? 'Edit Kata Kunci' : 'Tambah Kata Kunci'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-keyword">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="keywordName" class="form-control"
                        placeholder="Masukkan kata kunci">
                    @error('keywordName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-keyword" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- National Priority Modal -->
    <x-tabler.modal id="modal-national-priority" :title="$editingId ? 'Edit Prioritas Nasional' : 'Tambah Prioritas Nasional'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-national-priority">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="nationalPriorityName" class="form-control"
                        placeholder="Masukkan prioritas nasional">
                    @error('nationalPriorityName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-national-priority" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Partner Modal -->
    <x-tabler.modal id="modal-partner" :title="$editingId ? 'Edit Mitra' : 'Tambah Mitra'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-partner">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="partnerName" class="form-control"
                        placeholder="Masukkan nama mitra">
                    @error('partnerName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <input type="text" wire:model="partnerType" class="form-control"
                        placeholder="Masukkan tipe mitra">
                    @error('partnerType')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea wire:model="partnerAddress" class="form-control" rows="3" placeholder="Masukkan alamat mitra"></textarea>
                    @error('partnerAddress')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-partner" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Research Scheme Modal -->
    <x-tabler.modal id="modal-research-scheme" :title="$editingId ? 'Edit Skema Penelitian' : 'Tambah Skema Penelitian'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-research-scheme">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="researchSchemeName" class="form-control"
                        placeholder="Masukkan nama skema penelitian">
                    @error('researchSchemeName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Strata</label>
                    <select wire:model="researchSchemeStrata" class="form-select">
                        <option value="">Pilih Strata</option>
                        <option value="s1">S1</option>
                        <option value="s2">S2</option>
                        <option value="s3">S3</option>
                    </select>
                    @error('researchSchemeStrata')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-research-scheme" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Science Cluster Modal -->
    <x-tabler.modal id="modal-science-cluster" :title="$editingId ? 'Edit Klaster Sains' : 'Tambah Klaster Sains'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-science-cluster">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="scienceClusterName" class="form-control"
                        placeholder="Masukkan nama klaster sains">
                    @error('scienceClusterName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Parent Klaster (Opsional)</label>
                    <select wire:model="scienceClusterParentId" class="form-select">
                        <option value="">Tidak ada parent</option>
                        @foreach ($scienceClusters as $cluster)
                            <option value="{{ $cluster->id }}">{{ $cluster->name }}</option>
                        @endforeach
                    </select>
                    @error('scienceClusterParentId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-science-cluster" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Study Program Modal -->
    <x-tabler.modal id="modal-study-program" :title="$editingId ? 'Edit Program Studi' : 'Tambah Program Studi'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-study-program">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="studyProgramName" class="form-control"
                        placeholder="Masukkan nama program studi">
                    @error('studyProgramName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Institusi</label>
                    <select wire:model="institutionId" class="form-select">
                        <option value="">Pilih Institusi</option>
                        @foreach ($allInstitutions as $institution)
                            <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                        @endforeach
                    </select>
                    @error('institutionId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-study-program" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Theme Modal -->
    <x-tabler.modal id="modal-theme" :title="$editingId ? 'Edit Tema' : 'Tambah Tema'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-theme">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="themeName" class="form-control"
                        placeholder="Masukkan nama tema">
                    @error('themeName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Area Fokus</label>
                    <select wire:model="themesFocusAreaId" class="form-select">
                        <option value="">Pilih Area Fokus</option>
                        @foreach ($allFocusAreas as $focusArea)
                            <option value="{{ $focusArea->id }}">{{ $focusArea->name }}</option>
                        @endforeach
                    </select>
                    @error('themesFocusAreaId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-theme" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Topic Modal -->
    <x-tabler.modal id="modal-topic" :title="$editingId ? 'Edit Topik' : 'Tambah Topik'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-topic">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="topicName" class="form-control"
                        placeholder="Masukkan nama topik">
                    @error('topicName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tema</label>
                    <select wire:model="topicsThemeId" class="form-select">
                        <option value="">Pilih Tema</option>
                        @foreach ($allThemes as $theme)
                            <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                        @endforeach
                    </select>
                    @error('topicsThemeId')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-topic" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Institution Modal -->
    <x-tabler.modal id="modal-institution" :title="$editingId ? 'Edit Institusi' : 'Tambah Institusi'" on-hide="resetFormFields">
        <x-slot:body>
            <form wire:submit="save" id="form-institution">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" wire:model="institutionName" class="form-control"
                        placeholder="Masukkan nama institusi">
                    @error('institutionName')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </x-slot:body>
        <x-slot:footer>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" form="form-institution" class="btn btn-primary">Simpan</button>
        </x-slot:footer>
    </x-tabler.modal>

    <!-- Delete Confirmation Modal -->
    <x-tabler.modal id="modal-delete-confirm" title="Konfirmasi Penghapusan" size="sm" type="danger" on-hide="closeDeleteModal">
        <x-slot:body>
            <div class="text-center py-4">
                <x-lucide-alert-triangle class="mb-2 icon icon-lg text-danger" />
                <h3>{{ $deleteModalTitle }}</h3>
                <div class="text-secondary">{{ $deleteModalMessage }}</div>
            </div>
        </x-slot:body>
        <x-slot:footer>
            <div class="w-100">
                <div class="row">
                    <div class="col">
                        <button type="button" class="w-100 btn" data-bs-dismiss="modal">Batal</button>
                    </div>
                    <div class="col">
                        <button type="button" class="w-100 btn btn-danger" wire:click="confirmDeleteAction" data-bs-dismiss="modal">Hapus</button>
                    </div>
                </div>
            </div>
        </x-slot:footer>
    </x-tabler.modal>
@endteleport
</div>
