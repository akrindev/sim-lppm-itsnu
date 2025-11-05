<x-slot:title>Pengaturan</x-slot:title>
<x-slot:pageTitle>Master Data</x-slot:pageTitle>
<x-slot:pageSubtitle>Kelola semua data master sistem</x-slot:pageSubtitle>

<div>
    <div class="page-wrapper">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Kelola Data Master</h3>
                        </div>
                        <div class="card-body">
                            <!-- Research & Academic Content -->
                            <div class="mb-4">
                                <h4 class="mb-3">
                                    <x-lucide-book-open class="me-2 icon" />
                                    Konten Penelitian & Akademik
                                </h4>
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-target class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Area Fokus</h5>
                                                <p class="text-muted card-text">Kelola area fokus penelitian</p>
                                                <a href="{{ route('settings.tabs.focus-areas') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-hash class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Kata Kunci</h5>
                                                <p class="text-muted card-text">Kelola kata kunci penelitian</p>
                                                <a href="{{ route('settings.tabs.keywords') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-palette class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Tema</h5>
                                                <p class="text-muted card-text">Kelola tema penelitian</p>
                                                <a href="{{ route('settings.tabs.themes') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-message-square class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Topik</h5>
                                                <p class="text-muted card-text">Kelola topik penelitian</p>
                                                <a href="{{ route('settings.tabs.topics') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-file-text class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Skema Penelitian</h5>
                                                <p class="text-muted card-text">Kelola skema penelitian</p>
                                                <a href="{{ route('settings.tabs.research-schemes') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-atom class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Klaster Sains</h5>
                                                <p class="text-muted card-text">Kelola klaster sains</p>
                                                <a href="{{ route('settings.tabs.science-clusters') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Structure -->
                            <div class="mb-4">
                                <h4 class="mb-3">
                                    <x-lucide-school class="me-2 icon" />
                                    Struktur Akademik
                                </h4>
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-graduation-cap class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Program Studi</h5>
                                                <p class="text-muted card-text">Kelola program studi</p>
                                                <a href="{{ route('settings.tabs.study-programs') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-building class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Fakultas</h5>
                                                <p class="text-muted card-text">Kelola data fakultas</p>
                                                <a href="{{ route('settings.tabs.faculties') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-building-2 class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Institusi</h5>
                                                <p class="text-muted card-text">Kelola data institusi</p>
                                                <a href="{{ route('settings.tabs.institutions') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- External Partnerships -->
                            <div class="mb-4">
                                <h4 class="mb-3">
                                    <x-lucide-handshake class="me-2 icon" />
                                    Kemitraan & Prioritas
                                </h4>
                                <div class="row">
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-users class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Mitra</h5>
                                                <p class="text-muted card-text">Kelola data mitra</p>
                                                <a href="{{ route('settings.tabs.partners') }}" wire:navigate
                                                    class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 col-md-6 col-lg-4">
                                        <div class="h-100 card">
                                            <div class="text-center card-body">
                                                <x-lucide-star class="mb-2 text-primary icon" />
                                                <h5 class="card-title">Prioritas Nasional</h5>
                                                <p class="text-muted card-text">Kelola prioritas nasional</p>
                                                <a href="{{ route('settings.tabs.national-priorities') }}"
                                                    wire:navigate class="btn btn-primary">
                                                    Kelola
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
