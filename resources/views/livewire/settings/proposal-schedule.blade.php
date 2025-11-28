<div>
    <div class="page-header d-print-none">
        <div class="container-xl">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <h2 class="page-title">
                        Jadwal Proposal
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-12">
                    <form wire:submit.prevent="save">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Pengaturan Jadwal Proposal</h3>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Penelitian</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Mulai</label>
                                            <input type="date" class="form-control" wire:model="research_start_date">
                                            @error('research_start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Selesai</label>
                                            <input type="date" class="form-control" wire:model="research_end_date">
                                            @error('research_end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="mb-3">Pengabdian Masyarakat</h4>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Mulai</label>
                                            <input type="date" class="form-control"
                                                wire:model="community_service_start_date">
                                            @error('community_service_start_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Selesai</label>
                                            <input type="date" class="form-control"
                                                wire:model="community_service_end_date">
                                            @error('community_service_end_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
