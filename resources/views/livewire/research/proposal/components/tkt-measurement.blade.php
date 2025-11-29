<div>
    @teleport('body')
        <x-tabler.modal id="tkt-measurement-modal" title="Pengukuran TKT {{ $tktType ? '(' . $tktType . ')' : '' }}"
            size="xl">
            <x-slot:body>
                <div class="row" x-data="{ activeLevel: 1 }"
                    @@switch-level.window="activeLevel = $event.detail.level">
                    <!-- Level Tabs -->
                    <div class="col-md-3 border-end">
                        <div class="list-group">
                            @foreach ($levels as $level)
                                @php
                                    $isLocked = $this->isLevelLocked($level);
                                    $isPassed = $this->isLevelPassed($level->id);
                                    $average = $levelAverages[$level->id] ?? 0;
                                @endphp
                                <button type="button" class="list-group-item list-group-item-action"
                                    :class="{ 'active': activeLevel === {{ $level->level }} }"
                                    @click="activeLevel = {{ $level->level }}"
                                    @if ($isLocked) disabled style="opacity: 0.6; cursor: not-allowed;" @endif>
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1">Level {{ $level->level }}</h6>
                                        @if ($isPassed)
                                            <span class="badge bg-success">Pass</span>
                                        @elseif($isLocked)
                                            <span class="badge bg-secondary"><i class="ti ti-lock"></i></span>
                                        @else
                                            <span class="badge bg-warning">{{ $average }}%</span>
                                        @endif
                                    </div>
                                    <small class="d-block text-truncate mb-1">{{ $level->description }}</small>

                                    <!-- Mini Progress Bar -->
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar {{ $average >= 80 ? 'bg-success' : 'bg-warning' }}"
                                            role="progressbar" style="width: {{ $average }}%"
                                            aria-valuenow="{{ $average }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Indicators -->
                    <div class="col-md-9">
                        @foreach ($levels as $level)
                            <div x-show="activeLevel === {{ $level->level }}" class="p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Level {{ $level->level }}: {{ $level->description }}</h5>
                                    <div class="text-end">
                                        <span class="text-muted small">Rata-rata Level:</span>
                                        <span
                                            class="h3 {{ ($levelAverages[$level->id] ?? 0) >= 80 ? 'text-success' : 'text-warning' }} mb-0">
                                            {{ $levelAverages[$level->id] ?? 0 }}%
                                        </span>
                                        <div class="small text-muted">(Target: ≥ 80%)</div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    Pilih persentase capaian untuk setiap indikator. Level ini dianggap
                                    <strong>LULUS</strong> jika rata-rata capaian ≥ 80%.
                                </div>

                                <div class="list-group">
                                    @foreach ($level->indicators as $indicator)
                                        <div class="list-group-item">
                                            <div class="fw-bold mb-2">{{ $indicator->code }} - {{ $indicator->indicator }}
                                            </div>

                                            <div class="btn-group w-100" role="group" aria-label="Pilihan Persentase">
                                                @foreach ([0, 20, 40, 60, 80, 100] as $score)
                                                    <input type="radio" class="btn-check"
                                                        name="indicator_{{ $indicator->id }}"
                                                        id="btnradio_{{ $indicator->id }}_{{ $score }}"
                                                        value="{{ $score }}"
                                                        wire:model.live="indicatorScores.{{ $indicator->id }}"
                                                        autocomplete="off">
                                                    <label class="btn btn-outline-primary"
                                                        for="btnradio_{{ $indicator->id }}_{{ $score }}">{{ $score }}%</label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-slot:body>

            <x-slot:footer>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" wire:click="save" data-bs-dismiss="modal">
                    Simpan Hasil Pengukuran
                </button>
            </x-slot:footer>
        </x-tabler.modal>
    @endteleport
</div>
