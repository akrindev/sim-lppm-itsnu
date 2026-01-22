<?php

namespace App\Livewire\Settings;

use App\Livewire\Concerns\HasToast;
use App\Models\ReviewCriteria;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ReviewCriteriaManager extends Component
{
    use HasToast;

    public array $editing = [];

    public function mount(): void
    {
        if (! Auth::user()->hasRole('admin lppm')) {
            abort(403);
        }
    }

    #[Computed]
    public function researchCriterias()
    {
        return ReviewCriteria::where('type', 'research')
            ->orderBy('order')
            ->get();
    }

    #[Computed]
    public function pkmCriterias()
    {
        return ReviewCriteria::where('type', 'community_service')
            ->orderBy('order')
            ->get();
    }

    public function toggleActive(int $id): void
    {
        $criteria = ReviewCriteria::findOrFail($id);
        $criteria->update(['is_active' => ! $criteria->is_active]);
        $this->toastSuccess('Status kriteria berhasil diperbarui.');
    }

    public function edit(int $id): void
    {
        $criteria = ReviewCriteria::findOrFail($id);
        $this->editing = [
            'id' => $id,
            'criteria' => $criteria->criteria,
            'description' => $criteria->description,
            'weight' => $criteria->weight,
        ];
    }

    public function cancelEdit(): void
    {
        $this->editing = [];
    }

    public function save(): void
    {
        $this->validate([
            'editing.criteria' => 'required|string|max:255',
            'editing.description' => 'required|string',
            'editing.weight' => 'required|numeric|min:0|max:100',
        ], [], [
            'editing.criteria' => 'Nama Kriteria',
            'editing.description' => 'Deskripsi/Acuan',
            'editing.weight' => 'Bobot',
        ]);

        $criteria = ReviewCriteria::findOrFail($this->editing['id']);
        $criteria->update([
            'criteria' => $this->editing['criteria'],
            'description' => $this->editing['description'],
            'weight' => $this->editing['weight'],
        ]);

        $this->editing = [];
        $this->toastSuccess('Kriteria berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.settings.review-criteria-manager');
    }
}
