<?php

namespace App\Livewire\CommunityService\DailyNote;

use App\Livewire\Concerns\HasToast;
use App\Models\DailyNote;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Show extends Component
{
    use HasToast;
    use WithFileUploads;

    public Proposal $proposal;

    #[Validate('required|date|before_or_equal:today')]
    public string $activity_date = '';

    #[Validate('required|string|min:10')]
    public string $activity_description = '';

    #[Validate('required|integer|min:0|max:100')]
    public int $progress_percentage = 0;

    #[Validate('nullable|string')]
    public string $notes = '';

    #[Validate(['evidence.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'])] // 5MB max per file
    public $evidence = [];

    public ?string $editingId = null;

    public function mount(Proposal $proposal): void
    {
        if (! $this->canAccess($proposal)) {
            abort(403);
        }

        $this->proposal = $proposal;
        $this->activity_date = date('Y-m-d');
    }

    protected function canAccess(Proposal $proposal): bool
    {
        $userId = Auth::id();

        return $proposal->submitter_id === $userId ||
            $proposal->teamMembers()->where('user_id', $userId)->exists();
    }

    public function create(): void
    {
        $this->reset(['activity_description', 'progress_percentage', 'notes', 'evidence', 'editingId']);
        $this->activity_date = date('Y-m-d');
        $this->dispatch('open-modal', modalId: 'daily-note-modal');
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'proposal_id' => $this->proposal->id,
            'activity_date' => $this->activity_date,
            'activity_description' => $this->activity_description,
            'progress_percentage' => $this->progress_percentage,
            'notes' => $this->notes,
        ];

        if ($this->editingId) {
            $note = DailyNote::findOrFail($this->editingId);
            $note->update($data);
        } else {
            $note = DailyNote::create($data);
        }

        if ($this->evidence) {
            foreach ($this->evidence as $file) {
                $note->addMedia($file->getRealPath())
                    ->usingFileName($file->hashName())
                    ->toMediaCollection('evidence');
            }
        }

        $this->reset(['activity_description', 'progress_percentage', 'notes', 'evidence', 'editingId']);
        $this->activity_date = date('Y-m-d');
        $this->dispatch('note-saved');
        $this->dispatch('close-modal', modalId: 'daily-note-modal');
        $message = 'Catatan harian berhasil disimpan.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function edit(string $id): void
    {
        $note = DailyNote::findOrFail($id);

        if ($note->proposal_id !== $this->proposal->id) {
            abort(403);
        }

        $this->editingId = $id;
        $this->activity_date = $note->activity_date->format('Y-m-d');
        $this->activity_description = $note->activity_description;
        $this->progress_percentage = $note->progress_percentage;
        $this->notes = $note->notes ?? '';

        $this->dispatch('open-modal', modalId: 'daily-note-modal');
    }

    public function delete(string $id): void
    {
        $note = DailyNote::findOrFail($id);
        if ($note->proposal_id !== $this->proposal->id) {
            abort(403);
        }
        $note->delete();
        $message = 'Catatan harian berhasil dihapus.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    public function deleteEvidence(string $mediaId): void
    {
        $media = Media::findOrFail($mediaId);

        // Check if the media belongs to a note in this proposal
        $note = DailyNote::find($media->model_id);

        if ($note && $note->proposal_id === $this->proposal->id) {
            $media->delete();
            $message = 'File bukti berhasil dihapus.';
            session()->flash('success', $message);
            $this->toastSuccess($message);
        } else {
            abort(403);
        }
    }

    public function cancelEdit(): void
    {
        $this->reset(['activity_description', 'progress_percentage', 'notes', 'evidence', 'editingId']);
        $this->activity_date = date('Y-m-d');
        $this->dispatch('close-modal', modalId: 'daily-note-modal');
    }

    public function render()
    {
        return view('livewire.community-service.daily-note.show', [
            'notes_list' => $this->proposal->dailyNotes()->with('media')->latest('activity_date')->get(),
        ]);
    }
}
