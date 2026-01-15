<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\TktIndicator;
use App\Models\TktLevel;
use Livewire\Component;

/**
 * TKT Manager Component
 * Manages TKT Types, Levels, and Indicators with tabbed navigation
 */
class TktManager extends Component
{
    use HasToast;
    // Tab navigation
    public $currentTab = 'types'; // types, levels, indicators
    public $selectedType;
    public $selectedLevel;

    // Form properties
    public $typeName;
    public $levelDescription;
    public $indicatorCode;
    public $indicatorText;
    public $editingId;

    public function render()
    {
        $data = [];

        if ($this->currentTab === 'types') {
            $data['types'] = TktLevel::select('type')->distinct()->get()->pluck('type');
        } elseif ($this->currentTab === 'levels' && $this->selectedType) {
            $data['levels'] = TktLevel::where('type', $this->selectedType)->orderBy('level')->get();
        } elseif ($this->currentTab === 'indicators' && $this->selectedLevel) {
            $level = TktLevel::find($this->selectedLevel);
            $data['indicators'] = $level ? $level->indicators : collect();
            $data['levelInfo'] = $level;
        }

        return view('livewire.settings.tabs.tkt-manager', $data);
    }

    // --- Tab Navigation ---

    public function viewTypes()
    {
        $this->currentTab = 'types';
        $this->selectedType = null;
        $this->selectedLevel = null;
    }

    public function viewLevels($type)
    {
        $this->selectedType = $type;
        $this->currentTab = 'levels';
        $this->selectedLevel = null;
    }

    public function viewIndicators($levelId)
    {
        $this->selectedLevel = $levelId;
        $this->currentTab = 'indicators';
    }

    // --- Types CRUD ---

    public function createType()
    {
        $this->resetForm();
        $this->dispatch('open-modal', modalId: 'modal-type');
    }

    public function editType($type)
    {
        $this->typeName = $type;
        $this->editingId = $type; // Using name as ID for types
        $this->dispatch('open-modal', modalId: 'modal-type');
    }

    public function saveType()
    {
        $this->validate(['typeName' => 'required|string|max:255']);

        if ($this->editingId) {
            // Rename
            TktLevel::where('type', $this->editingId)->update(['type' => $this->typeName]);
        } else {
            // Create new (with 9 levels)
            for ($i = 1; $i <= 9; $i++) {
                TktLevel::create([
                    'type' => $this->typeName,
                    'level' => $i,
                    'description' => 'Deskripsi Level ' . $i,
                ]);
            }
        }

        $message = $this->editingId ? 'Kategori TKT berhasil diubah' : 'Kategori TKT berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);
        $this->dispatch('close-modal', modalId: 'modal-type');
        $this->resetForm();
    }

    public function deleteType($type)
    {
        TktLevel::where('type', $type)->delete();
        $message = 'Kategori TKT berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    // --- Levels CRUD ---

    public function editLevel($levelId)
    {
        $level = TktLevel::find($levelId);
        if (!$level) return;

        $this->editingId = $levelId;
        $this->levelDescription = $level->description;
        $this->dispatch('open-modal', modalId: 'modal-level');
    }

    public function saveLevel()
    {
        $this->validate(['levelDescription' => 'required|string']);

        TktLevel::find($this->editingId)->update(['description' => $this->levelDescription]);

        $message = 'Level TKT berhasil diperbarui';
        session()->flash('success', $message);
        $this->toastSuccess($message);
        $this->dispatch('close-modal', modalId: 'modal-level');
        $this->resetForm();
    }

    // --- Indicators CRUD ---

    public function createIndicator()
    {
        $this->resetForm();
        $this->dispatch('open-modal', modalId: 'modal-indicator');
    }

    public function editIndicator($indicatorId)
    {
        $indicator = TktIndicator::find($indicatorId);
        if (!$indicator) return;

        $this->editingId = $indicatorId;
        $this->indicatorCode = $indicator->code;
        $this->indicatorText = $indicator->indicator;
        $this->dispatch('open-modal', modalId: 'modal-indicator');
    }

    public function saveIndicator()
    {
        $this->validate([
            'indicatorCode' => 'nullable|string|max:10',
            'indicatorText' => 'required|string',
        ]);

        if ($this->editingId) {
            TktIndicator::find($this->editingId)->update([
                'code' => $this->indicatorCode,
                'indicator' => $this->indicatorText,
            ]);
        } else {
            TktIndicator::create([
                'tkt_level_id' => $this->selectedLevel,
                'code' => $this->indicatorCode,
                'indicator' => $this->indicatorText,
            ]);
        }

        $message = $this->editingId ? 'Indikator berhasil diubah' : 'Indikator berhasil ditambahkan';
        session()->flash('success', $message);
        $this->toastSuccess($message);
        $this->dispatch('close-modal', modalId: 'modal-indicator');
        $this->resetForm();
    }

    public function deleteIndicator($id)
    {
        TktIndicator::find($id)?->delete();
        $message = 'Indikator berhasil dihapus';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    private function resetForm()
    {
        $this->typeName = '';
        $this->levelDescription = '';
        $this->indicatorCode = '';
        $this->indicatorText = '';
        $this->editingId = null;
    }
}
