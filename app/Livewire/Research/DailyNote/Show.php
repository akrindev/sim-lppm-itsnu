<?php

namespace App\Livewire\Research\DailyNote;

use App\Models\DailyNote;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Proposal $proposal;

    #[Validate('required|date')]
    public string $activity_date = '';

    #[Validate('required|string|min:10')]
    public string $activity_description = '';

    #[Validate('required|integer|min:0|max:100')]
    public int $progress_percentage = 0;

    #[Validate('nullable|string')]
    public string $notes = '';

    public $evidence;

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

        // Check if submitter or team member
        return $proposal->submitter_id === $userId ||
            $proposal->teamMembers()->where('user_id', $userId)->exists();
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
            $note->clearMediaCollection('evidence');
            $note->addMedia($this->evidence->getRealPath())
                ->usingFileName($this->evidence->hashName())
                ->toMediaCollection('evidence');
        }

        $this->reset(['activity_description', 'progress_percentage', 'notes', 'evidence', 'editingId']);
        $this->activity_date = date('Y-m-d');
        $this->dispatch('note-saved');
        session()->flash('message', 'Catatan harian berhasil disimpan.');
    }

    public function edit(string $id): void
    {
        $note = DailyNote::findOrFail($id);
        $this->editingId = $id;
        $this->activity_date = $note->activity_date->format('Y-m-d');
        $this->activity_description = $note->activity_description;
        $this->progress_percentage = $note->progress_percentage;
        $this->notes = $note->notes ?? '';
    }

    public function delete(string $id): void
    {
        $note = DailyNote::findOrFail($id);
        $note->delete();
        session()->flash('message', 'Catatan harian berhasil dihapus.');
    }

    public function cancelEdit(): void
    {
        $this->reset(['activity_description', 'progress_percentage', 'notes', 'evidence', 'editingId']);
        $this->activity_date = date('Y-m-d');
    }

    public function render()
    {
        return view('livewire.research.daily-note.show', [
            'notes_list' => $this->proposal->dailyNotes()->latest('activity_date')->get(),
        ]);
    }
}
