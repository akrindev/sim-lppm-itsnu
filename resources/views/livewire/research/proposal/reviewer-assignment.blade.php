<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tugaskan Reviewer</h3>
    </div>
    <div class="card-body">
        @if ($this->availableReviewers->count() > 0)
            <div class="mb-3">
                <label class="form-label">Pilih Reviewer</label>
                <select wire:model="selectedReviewers" multiple class="form-select" required>
                    @foreach ($this->availableReviewers as $reviewer)
                        <option value="{{ $reviewer->id }}">{{ $reviewer->name }} ({{ $reviewer->email }})</option>
                    @endforeach
                </select>
                <small class="text-muted form-text">Tahan Ctrl/Cmd untuk memilih multiple</small>
            </div>
            <button wire:click="assignReviewers" class="btn btn-primary">
                <x-lucide-send class="icon" />
                Tugaskan Reviewer
            </button>
        @else
            <div class="alert alert-info" role="alert">
                Semua reviewer sudah ditugaskan untuk proposal ini
            </div>
        @endif
    </div>

    @if ($this->currentReviewers->count() > 0)
        <div class="card-header">
            <h3 class="card-title">Reviewer yang Ditugaskan</h3>
        </div>
        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="w-1">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($this->currentReviewers as $reviewer)
                        <tr>
                            <td>{{ $reviewer->user->name }}</td>
                            <td>{{ $reviewer->user->email }}</td>
                            <td>
                                @if ($reviewer->status === 'pending')
                                    <span class="bg-warning badge">Menunggu</span>
                                @elseif ($reviewer->status === 'reviewing')
                                    <span class="bg-info badge">Sedang Review</span>
                                @elseif ($reviewer->status === 'completed')
                                    <span class="bg-success badge">Selesai</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="removeReviewer('{{ $reviewer->user->id }}')"
                                    class="btn btn-icon btn-ghost-danger btn-sm" wire:confirm="Hapus reviewer ini?">
                                    <x-lucide-trash-2 class="icon" />
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
