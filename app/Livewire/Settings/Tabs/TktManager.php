<?php

namespace App\Livewire\Settings\Tabs;

use App\Livewire\Concerns\HasToast;
use App\Models\TktIndicator;
use App\Models\TktLevel;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * TKT Manager Component - Master-Detail Split Panel
 * Manages TKT Types, Levels, and Indicators with tree navigation
 */
class TktManager extends Component
{
    use HasToast;

    // Navigation state
    #[Url(as: 'type')]
    public ?string $selectedType = null;

    #[Url(as: 'level')]
    public ?int $selectedLevelId = null;

    public string $search = '';

    // Expanded types in tree view (track which categories are expanded)
    public array $expandedTypes = [];

    // Inline editing state
    public bool $editingLevelDesc = false;

    public string $levelDescriptionInput = '';

    public ?int $editingIndicatorId = null;

    public string $indicatorCodeInput = '';

    public string $indicatorTextInput = '';

    // Adding new indicator
    public bool $addingIndicator = false;

    public string $newIndicatorCode = '';

    public string $newIndicatorText = '';

    // Category management
    public bool $addingCategory = false;

    public string $newCategoryName = '';

    public ?string $editingCategoryName = null;

    public string $categoryNameInput = '';

    public string $deleteType = '';

    public ?int $deleteIndicatorId = null;

    public string $deleteIndicatorName = '';

    public function confirmDeleteCategory(string $type): void
    {
        $this->deleteType = $type;
    }

    public function handleConfirmDeleteCategory(): void
    {
        if ($this->deleteType) {
            $this->deleteCategory($this->deleteType);
            $this->deleteType = '';
        }
    }

    public function confirmDeleteIndicator(int $id, string $name): void
    {
        $this->deleteIndicatorId = $id;
        $this->deleteIndicatorName = $name;
    }

    public function handleConfirmDeleteIndicator(): void
    {
        if ($this->deleteIndicatorId) {
            $this->deleteIndicator($this->deleteIndicatorId);
            $this->deleteIndicatorId = null;
            $this->deleteIndicatorName = '';
        }
    }

    public function mount(): void
    {
        // Auto-expand and select first type if none selected
        if ($this->selectedType === null) {
            $firstType = TktLevel::select('type')->distinct()->orderBy('type')->first()?->type;
            if ($firstType) {
                $this->selectedType = $firstType;
                $this->expandedTypes = [$firstType];

                // Auto-select first level
                $firstLevel = TktLevel::where('type', $firstType)->orderBy('level')->first();
                if ($firstLevel) {
                    $this->selectedLevelId = $firstLevel->id;
                }
            }
        } else {
            // Ensure selected type is expanded
            $this->expandedTypes = [$this->selectedType];
        }
    }

    public function render()
    {
        return view('livewire.settings.tabs.tkt-manager');
    }

    // =====================
    // COMPUTED PROPERTIES
    // =====================

    #[Computed]
    public function types(): Collection
    {
        $query = TktLevel::select('type')
            ->distinct()
            ->orderBy('type');

        if ($this->search) {
            $query->where('type', 'like', '%'.$this->search.'%');
        }

        return $query->pluck('type');
    }

    #[Computed]
    public function typesWithLevels(): Collection
    {
        $query = TktLevel::query()
            ->with('indicators')
            ->orderBy('type')
            ->orderBy('level');

        if ($this->search) {
            $query->where('type', 'like', '%'.$this->search.'%');
        }

        return $query->get()->groupBy('type');
    }

    #[Computed]
    public function selectedLevel(): ?TktLevel
    {
        if (! $this->selectedLevelId) {
            return null;
        }

        return TktLevel::with('indicators')->find($this->selectedLevelId);
    }

    #[Computed]
    public function indicators(): Collection
    {
        if (! $this->selectedLevel) {
            return collect();
        }

        return $this->selectedLevel->indicators->sortBy('code');
    }

    // =====================
    // TREE VIEW NAVIGATION
    // =====================

    public function toggleType(string $type): void
    {
        if (in_array($type, $this->expandedTypes)) {
            $this->expandedTypes = array_values(array_diff($this->expandedTypes, [$type]));
        } else {
            $this->expandedTypes[] = $type;
        }
    }

    public function selectLevel(int $levelId): void
    {
        $level = TktLevel::find($levelId);
        if (! $level) {
            return;
        }

        $this->selectedLevelId = $levelId;
        $this->selectedType = $level->type;

        // Ensure parent type is expanded
        if (! in_array($level->type, $this->expandedTypes)) {
            $this->expandedTypes[] = $level->type;
        }

        // Reset editing states
        $this->cancelAllEditing();
    }

    public function isTypeExpanded(string $type): bool
    {
        return in_array($type, $this->expandedTypes);
    }

    // =====================
    // CATEGORY CRUD
    // =====================

    public function startAddCategory(): void
    {
        $this->addingCategory = true;
        $this->newCategoryName = '';
    }

    public function cancelAddCategory(): void
    {
        $this->addingCategory = false;
        $this->newCategoryName = '';
    }

    public function saveNewCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:255',
        ], [], [
            'newCategoryName' => 'Nama Kategori',
        ]);

        // Check if category already exists
        $exists = TktLevel::where('type', $this->newCategoryName)->exists();
        if ($exists) {
            $this->addError('newCategoryName', 'Kategori dengan nama ini sudah ada.');

            return;
        }

        // Create 9 levels for new category
        for ($i = 1; $i <= 9; $i++) {
            TktLevel::create([
                'type' => $this->newCategoryName,
                'level' => $i,
                'description' => 'Deskripsi Level '.$i,
            ]);
        }

        $this->toastSuccess('Kategori TKT berhasil ditambahkan');
        $this->addingCategory = false;
        $this->newCategoryName = '';

        // Auto-select the new category
        $this->selectedType = $this->newCategoryName;
        $this->expandedTypes[] = $this->newCategoryName;

        $firstLevel = TktLevel::where('type', $this->newCategoryName)->orderBy('level')->first();
        if ($firstLevel) {
            $this->selectedLevelId = $firstLevel->id;
        }

        unset($this->types, $this->typesWithLevels);
    }

    public function startEditCategory(string $type): void
    {
        $this->editingCategoryName = $type;
        $this->categoryNameInput = $type;
    }

    public function cancelEditCategory(): void
    {
        $this->editingCategoryName = null;
        $this->categoryNameInput = '';
    }

    public function saveCategory(): void
    {
        $this->validate([
            'categoryNameInput' => 'required|string|max:255',
        ], [], [
            'categoryNameInput' => 'Nama Kategori',
        ]);

        if ($this->categoryNameInput === $this->editingCategoryName) {
            $this->cancelEditCategory();

            return;
        }

        // Check if new name already exists
        $exists = TktLevel::where('type', $this->categoryNameInput)
            ->where('type', '!=', $this->editingCategoryName)
            ->exists();

        if ($exists) {
            $this->addError('categoryNameInput', 'Kategori dengan nama ini sudah ada.');

            return;
        }

        TktLevel::where('type', $this->editingCategoryName)
            ->update(['type' => $this->categoryNameInput]);

        // Update selected type if it was renamed
        if ($this->selectedType === $this->editingCategoryName) {
            $this->selectedType = $this->categoryNameInput;
        }

        // Update expanded types
        $index = array_search($this->editingCategoryName, $this->expandedTypes);
        if ($index !== false) {
            $this->expandedTypes[$index] = $this->categoryNameInput;
        }

        $this->toastSuccess('Kategori TKT berhasil diubah');
        $this->cancelEditCategory();

        unset($this->types, $this->typesWithLevels);
    }

    public function deleteCategory(string $type): void
    {
        TktLevel::where('type', $type)->delete();

        // Reset selection if deleted category was selected
        if ($this->selectedType === $type) {
            $this->selectedType = null;
            $this->selectedLevelId = null;

            // Select first available type
            $firstType = $this->types->first();
            if ($firstType) {
                $this->selectedType = $firstType;
                $this->expandedTypes = [$firstType];

                $firstLevel = TktLevel::where('type', $firstType)->orderBy('level')->first();
                if ($firstLevel) {
                    $this->selectedLevelId = $firstLevel->id;
                }
            }
        }

        // Remove from expanded
        $this->expandedTypes = array_values(array_diff($this->expandedTypes, [$type]));

        $this->toastSuccess('Kategori TKT berhasil dihapus');

        unset($this->types, $this->typesWithLevels);
    }

    // =====================
    // LEVEL INLINE EDIT
    // =====================

    public function startEditLevelDesc(): void
    {
        if (! $this->selectedLevel) {
            return;
        }

        $this->editingLevelDesc = true;
        $this->levelDescriptionInput = $this->selectedLevel->description ?? '';
    }

    public function cancelEditLevelDesc(): void
    {
        $this->editingLevelDesc = false;
        $this->levelDescriptionInput = '';
    }

    public function saveLevelDesc(): void
    {
        if (! $this->selectedLevel) {
            return;
        }

        $this->validate([
            'levelDescriptionInput' => 'required|string',
        ], [], [
            'levelDescriptionInput' => 'Deskripsi Level',
        ]);

        $this->selectedLevel->update([
            'description' => $this->levelDescriptionInput,
        ]);

        $this->editingLevelDesc = false;
        $this->toastSuccess('Deskripsi level berhasil disimpan');

        unset($this->selectedLevel, $this->typesWithLevels);
    }

    // =====================
    // INDICATOR CRUD
    // =====================

    public function startAddIndicator(): void
    {
        $this->addingIndicator = true;
        $this->newIndicatorCode = '';
        $this->newIndicatorText = '';
        $this->editingIndicatorId = null;
    }

    public function cancelAddIndicator(): void
    {
        $this->addingIndicator = false;
        $this->newIndicatorCode = '';
        $this->newIndicatorText = '';
    }

    public function saveNewIndicator(): void
    {
        if (! $this->selectedLevelId) {
            return;
        }

        $this->validate([
            'newIndicatorCode' => 'nullable|string|max:10',
            'newIndicatorText' => 'required|string',
        ], [], [
            'newIndicatorCode' => 'Kode',
            'newIndicatorText' => 'Indikator',
        ]);

        TktIndicator::create([
            'tkt_level_id' => $this->selectedLevelId,
            'code' => $this->newIndicatorCode ?: null,
            'indicator' => $this->newIndicatorText,
        ]);

        $this->addingIndicator = false;
        $this->newIndicatorCode = '';
        $this->newIndicatorText = '';

        $this->toastSuccess('Indikator berhasil ditambahkan');

        unset($this->selectedLevel, $this->indicators, $this->typesWithLevels);
    }

    public function startEditIndicator(int $indicatorId): void
    {
        $indicator = TktIndicator::find($indicatorId);
        if (! $indicator) {
            return;
        }

        $this->editingIndicatorId = $indicatorId;
        $this->indicatorCodeInput = $indicator->code ?? '';
        $this->indicatorTextInput = $indicator->indicator ?? '';
        $this->addingIndicator = false;
    }

    public function cancelEditIndicator(): void
    {
        $this->editingIndicatorId = null;
        $this->indicatorCodeInput = '';
        $this->indicatorTextInput = '';
    }

    public function saveIndicator(): void
    {
        if (! $this->editingIndicatorId) {
            return;
        }

        $this->validate([
            'indicatorCodeInput' => 'nullable|string|max:10',
            'indicatorTextInput' => 'required|string',
        ], [], [
            'indicatorCodeInput' => 'Kode',
            'indicatorTextInput' => 'Indikator',
        ]);

        $indicator = TktIndicator::find($this->editingIndicatorId);
        if ($indicator) {
            $indicator->update([
                'code' => $this->indicatorCodeInput ?: null,
                'indicator' => $this->indicatorTextInput,
            ]);
        }

        $this->editingIndicatorId = null;
        $this->indicatorCodeInput = '';
        $this->indicatorTextInput = '';

        $this->toastSuccess('Indikator berhasil disimpan');

        unset($this->selectedLevel, $this->indicators, $this->typesWithLevels);
    }

    public function deleteIndicator(int $indicatorId): void
    {
        TktIndicator::find($indicatorId)?->delete();

        $this->toastSuccess('Indikator berhasil dihapus');

        unset($this->selectedLevel, $this->indicators, $this->typesWithLevels);
    }

    // =====================
    // HELPERS
    // =====================

    public function cancelAllEditing(): void
    {
        $this->editingLevelDesc = false;
        $this->levelDescriptionInput = '';
        $this->editingIndicatorId = null;
        $this->indicatorCodeInput = '';
        $this->indicatorTextInput = '';
        $this->addingIndicator = false;
        $this->newIndicatorCode = '';
        $this->newIndicatorText = '';
        $this->addingCategory = false;
        $this->newCategoryName = '';
        $this->editingCategoryName = null;
        $this->categoryNameInput = '';
    }

    public function updatedSearch(): void
    {
        unset($this->types, $this->typesWithLevels);
    }

    public function getLevelIndicatorCount(int $levelId): int
    {
        return TktIndicator::where('tkt_level_id', $levelId)->count();
    }
}
