<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Models\FocusArea;
use App\Models\Institution;
use App\Models\Keyword;
use App\Models\NationalPriority;
use App\Models\Partner;
use App\Models\ResearchScheme;
use App\Models\ScienceCluster;
use App\Models\StudyProgram;
use App\Models\Theme;
use App\Models\Topic;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class MasterData extends Component
{
    use WithPagination;

    public string $activeTab = 'focus-areas';

    // Focus Areas
    #[Validate('required|min:3|max:255')]
    public string $focusAreaName = '';

    // Keywords
    #[Validate('required|min:3|max:255')]
    public string $keywordName = '';

    // National Priorities
    #[Validate('required|min:3|max:255')]
    public string $nationalPriorityName = '';

    // Partners
    #[Validate('required|min:3|max:255')]
    public string $partnerName = '';

    #[Validate('nullable|max:255')]
    public string $partnerType = '';

    #[Validate('nullable|max:1000')]
    public string $partnerAddress = '';

    // Research Schemes
    #[Validate('required|min:3|max:255')]
    public string $researchSchemeName = '';

    #[Validate('required')]
    public string $researchSchemeStrata = '';

    // Science Clusters
    #[Validate('required|min:3|max:255')]
    public string $scienceClusterName = '';

    #[Validate('nullable')]
    public ?int $scienceClusterParentId = null;

    // Study Programs
    #[Validate('required|min:3|max:255')]
    public string $studyProgramName = '';

    #[Validate('required')]
    public ?int $institutionId = null;

    // Themes
    #[Validate('required|min:3|max:255')]
    public string $themeName = '';

    #[Validate('required')]
    public ?int $themesFocusAreaId = null;

    // Topics
    #[Validate('required|min:3|max:255')]
    public string $topicName = '';

    #[Validate('required')]
    public ?int $topicsThemeId = null;

    // Institutions
    #[Validate('required|min:3|max:255')]
    public string $institutionName = '';

    public string $modalTitle = '';

    public ?int $editingId = null;

    public string $modalFor = '';

    public ?int $deletingId = null;

    public string $deleteModalTitle = '';

    public string $deleteModalMessage = '';

    public string $deletingType = '';

    public function render()
    {
        return view('livewire.settings.master-data', [
            'focusAreas' => FocusArea::paginate(10),
            'keywords' => Keyword::paginate(10),
            'nationalPriorities' => NationalPriority::paginate(10),
            'partners' => Partner::paginate(10),
            'researchSchemes' => ResearchScheme::paginate(10),
            'scienceClusters' => ScienceCluster::whereNull('parent_id')->paginate(10),
            'studyPrograms' => StudyProgram::with('institution')->paginate(10),
            'themes' => Theme::with('focusArea')->paginate(10),
            'topics' => Topic::with('theme')->paginate(10),
            'institutions' => Institution::paginate(10),
            'allInstitutions' => Institution::all(),
            'allFocusAreas' => FocusArea::all(),
            'allThemes' => Theme::all(),
        ]);
    }

    // Focus Areas
    public function createFocusArea(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'focus-area';
        $this->modalTitle = 'Tambah Area Fokus';

    }

    public function saveFocusArea(): void
    {
        $this->validate(['focusAreaName' => 'required|min:3|max:255']);

        if ($this->editingId) {
            FocusArea::findOrFail($this->editingId)->update(['name' => $this->focusAreaName]);
        } else {
            FocusArea::create(['name' => $this->focusAreaName]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Area Fokus berhasil diubah' : 'Area Fokus berhasil ditambahkan', type: 'success');
    }

    public function editFocusArea(FocusArea $focusArea): void
    {
        $this->editingId = $focusArea->id;
        $this->focusAreaName = $focusArea->name;
        $this->modalFor = 'focus-area';
        $this->modalTitle = 'Edit Area Fokus';

    }

    public function deleteFocusArea(FocusArea $focusArea): void
    {
        $focusArea->delete();
        $this->dispatch('notify', message: 'Area Fokus berhasil dihapus', type: 'success');
    }

    // Keywords
    public function createKeyword(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'keyword';
        $this->modalTitle = 'Tambah Kata Kunci';

    }

    public function saveKeyword(): void
    {
        $this->validate(['keywordName' => 'required|min:3|max:255']);

        if ($this->editingId) {
            Keyword::findOrFail($this->editingId)->update(['name' => $this->keywordName]);
        } else {
            Keyword::create(['name' => $this->keywordName]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Kata Kunci berhasil diubah' : 'Kata Kunci berhasil ditambahkan', type: 'success');
    }

    public function editKeyword(Keyword $keyword): void
    {
        $this->editingId = $keyword->id;
        $this->keywordName = $keyword->name;
        $this->modalFor = 'keyword';
        $this->modalTitle = 'Edit Kata Kunci';

    }

    public function deleteKeyword(Keyword $keyword): void
    {
        $keyword->delete();
        $this->dispatch('notify', message: 'Kata Kunci berhasil dihapus', type: 'success');
    }

    // National Priorities
    public function createNationalPriority(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'national-priority';
        $this->modalTitle = 'Tambah Prioritas Nasional';

    }

    public function saveNationalPriority(): void
    {
        $this->validate(['nationalPriorityName' => 'required|min:3|max:255']);

        if ($this->editingId) {
            NationalPriority::findOrFail($this->editingId)->update(['name' => $this->nationalPriorityName]);
        } else {
            NationalPriority::create(['name' => $this->nationalPriorityName]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Prioritas Nasional berhasil diubah' : 'Prioritas Nasional berhasil ditambahkan', type: 'success');
    }

    public function editNationalPriority(NationalPriority $nationalPriority): void
    {
        $this->editingId = $nationalPriority->id;
        $this->nationalPriorityName = $nationalPriority->name;
        $this->modalFor = 'national-priority';
        $this->modalTitle = 'Edit Prioritas Nasional';

    }

    public function deleteNationalPriority(NationalPriority $nationalPriority): void
    {
        $nationalPriority->delete();
        $this->dispatch('notify', message: 'Prioritas Nasional berhasil dihapus', type: 'success');
    }

    // Partners
    public function createPartner(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'partner';
        $this->modalTitle = 'Tambah Mitra';

    }

    public function savePartner(): void
    {
        $this->validate([
            'partnerName' => 'required|min:3|max:255',
            'partnerType' => 'nullable|max:255',
            'partnerAddress' => 'nullable|max:1000',
        ]);

        if ($this->editingId) {
            Partner::findOrFail($this->editingId)->update([
                'name' => $this->partnerName,
                'type' => $this->partnerType,
                'address' => $this->partnerAddress,
            ]);
        } else {
            Partner::create([
                'name' => $this->partnerName,
                'type' => $this->partnerType,
                'address' => $this->partnerAddress,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Mitra berhasil diubah' : 'Mitra berhasil ditambahkan', type: 'success');
    }

    public function editPartner(Partner $partner): void
    {
        $this->editingId = $partner->id;
        $this->partnerName = $partner->name;
        $this->partnerType = $partner->type ?? '';
        $this->partnerAddress = $partner->address ?? '';
        $this->modalFor = 'partner';
        $this->modalTitle = 'Edit Mitra';

    }

    public function deletePartner(Partner $partner): void
    {
        $partner->delete();
        $this->dispatch('notify', message: 'Mitra berhasil dihapus', type: 'success');
    }

    // Research Schemes
    public function createResearchScheme(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'research-scheme';
        $this->modalTitle = 'Tambah Skema Penelitian';

    }

    public function saveResearchScheme(): void
    {
        $this->validate([
            'researchSchemeName' => 'required|min:3|max:255',
            'researchSchemeStrata' => 'required',
        ]);

        if ($this->editingId) {
            ResearchScheme::findOrFail($this->editingId)->update([
                'name' => $this->researchSchemeName,
                'strata' => $this->researchSchemeStrata,
            ]);
        } else {
            ResearchScheme::create([
                'name' => $this->researchSchemeName,
                'strata' => $this->researchSchemeStrata,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Skema Penelitian berhasil diubah' : 'Skema Penelitian berhasil ditambahkan', type: 'success');
    }

    public function editResearchScheme(ResearchScheme $researchScheme): void
    {
        $this->editingId = $researchScheme->id;
        $this->researchSchemeName = $researchScheme->name;
        $this->researchSchemeStrata = $researchScheme->strata;
        $this->modalFor = 'research-scheme';
        $this->modalTitle = 'Edit Skema Penelitian';

    }

    public function deleteResearchScheme(ResearchScheme $researchScheme): void
    {
        $researchScheme->delete();
        $this->dispatch('notify', message: 'Skema Penelitian berhasil dihapus', type: 'success');
    }

    // Science Clusters
    public function createScienceCluster(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'science-cluster';
        $this->modalTitle = 'Tambah Klaster Sains';

    }

    public function saveScienceCluster(): void
    {
        $this->validate([
            'scienceClusterName' => 'required|min:3|max:255',
            'scienceClusterParentId' => 'nullable|exists:science_clusters,id',
        ]);

        if ($this->editingId) {
            ScienceCluster::findOrFail($this->editingId)->update([
                'name' => $this->scienceClusterName,
                'parent_id' => $this->scienceClusterParentId,
            ]);
        } else {
            ScienceCluster::create([
                'name' => $this->scienceClusterName,
                'parent_id' => $this->scienceClusterParentId,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Klaster Sains berhasil diubah' : 'Klaster Sains berhasil ditambahkan', type: 'success');
    }

    public function editScienceCluster(ScienceCluster $scienceCluster): void
    {
        $this->editingId = $scienceCluster->id;
        $this->scienceClusterName = $scienceCluster->name;
        $this->scienceClusterParentId = $scienceCluster->parent_id;
        $this->modalFor = 'science-cluster';
        $this->modalTitle = 'Edit Klaster Sains';

    }

    public function deleteScienceCluster(ScienceCluster $scienceCluster): void
    {
        $scienceCluster->delete();
        $this->dispatch('notify', message: 'Klaster Sains berhasil dihapus', type: 'success');
    }

    // Study Programs
    public function createStudyProgram(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'study-program';
        $this->modalTitle = 'Tambah Program Studi';

    }

    public function saveStudyProgram(): void
    {
        $this->validate([
            'studyProgramName' => 'required|min:3|max:255',
            'institutionId' => 'required|exists:institutions,id',
        ]);

        if ($this->editingId) {
            StudyProgram::findOrFail($this->editingId)->update([
                'name' => $this->studyProgramName,
                'institution_id' => $this->institutionId,
            ]);
        } else {
            StudyProgram::create([
                'name' => $this->studyProgramName,
                'institution_id' => $this->institutionId,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Program Studi berhasil diubah' : 'Program Studi berhasil ditambahkan', type: 'success');
    }

    public function editStudyProgram(StudyProgram $studyProgram): void
    {
        $this->editingId = $studyProgram->id;
        $this->studyProgramName = $studyProgram->name;
        $this->institutionId = $studyProgram->institution_id;
        $this->modalFor = 'study-program';
        $this->modalTitle = 'Edit Program Studi';

    }

    public function deleteStudyProgram(StudyProgram $studyProgram): void
    {
        $studyProgram->delete();
        $this->dispatch('notify', message: 'Program Studi berhasil dihapus', type: 'success');
    }

    // Themes
    public function createTheme(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'theme';
        $this->modalTitle = 'Tambah Tema';

    }

    public function saveTheme(): void
    {
        $this->validate([
            'themeName' => 'required|min:3|max:255',
            'themesFocusAreaId' => 'required|exists:focus_areas,id',
        ]);

        if ($this->editingId) {
            Theme::findOrFail($this->editingId)->update([
                'name' => $this->themeName,
                'focus_area_id' => $this->themesFocusAreaId,
            ]);
        } else {
            Theme::create([
                'name' => $this->themeName,
                'focus_area_id' => $this->themesFocusAreaId,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Tema berhasil diubah' : 'Tema berhasil ditambahkan', type: 'success');
    }

    public function editTheme(Theme $theme): void
    {
        $this->editingId = $theme->id;
        $this->themeName = $theme->name;
        $this->themesFocusAreaId = $theme->focus_area_id;
        $this->modalFor = 'theme';
        $this->modalTitle = 'Edit Tema';

    }

    public function deleteTheme(Theme $theme): void
    {
        $theme->delete();
        $this->dispatch('notify', message: 'Tema berhasil dihapus', type: 'success');
    }

    // Topics
    public function createTopic(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'topic';
        $this->modalTitle = 'Tambah Topik';

    }

    public function saveTopic(): void
    {
        $this->validate([
            'topicName' => 'required|min:3|max:255',
            'topicsThemeId' => 'required|exists:themes,id',
        ]);

        if ($this->editingId) {
            Topic::findOrFail($this->editingId)->update([
                'name' => $this->topicName,
                'theme_id' => $this->topicsThemeId,
            ]);
        } else {
            Topic::create([
                'name' => $this->topicName,
                'theme_id' => $this->topicsThemeId,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Topik berhasil diubah' : 'Topik berhasil ditambahkan', type: 'success');
    }

    public function editTopic(Topic $topic): void
    {
        $this->editingId = $topic->id;
        $this->topicName = $topic->name;
        $this->topicsThemeId = $topic->theme_id;
        $this->modalFor = 'topic';
        $this->modalTitle = 'Edit Topik';

    }

    public function deleteTopic(Topic $topic): void
    {
        $topic->delete();
        $this->dispatch('notify', message: 'Topik berhasil dihapus', type: 'success');
    }

    // Institutions
    public function createInstitution(): void
    {
        $this->resetFormFields();
        $this->modalFor = 'institution';
        $this->modalTitle = 'Tambah Institusi';

    }

    public function saveInstitution(): void
    {
        $this->validate(['institutionName' => 'required|min:3|max:255']);

        if ($this->editingId) {
            Institution::findOrFail($this->editingId)->update(['name' => $this->institutionName]);
        } else {
            Institution::create(['name' => $this->institutionName]);
        }

        $this->closeModal();
        $this->dispatch('notify', message: $this->editingId ? 'Institusi berhasil diubah' : 'Institusi berhasil ditambahkan', type: 'success');
    }

    public function editInstitution(Institution $institution): void
    {
        $this->editingId = $institution->id;
        $this->institutionName = $institution->name;
        $this->modalFor = 'institution';
        $this->modalTitle = 'Edit Institusi';

    }

    public function deleteInstitution(Institution $institution): void
    {
        $institution->delete();
        $this->dispatch('notify', message: 'Institusi berhasil dihapus', type: 'success');
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    private function resetFormFields(): void
    {
        $this->focusAreaName = '';
        $this->keywordName = '';
        $this->nationalPriorityName = '';
        $this->partnerName = '';
        $this->partnerType = '';
        $this->partnerAddress = '';
        $this->researchSchemeName = '';
        $this->researchSchemeStrata = '';
        $this->scienceClusterName = '';
        $this->scienceClusterParentId = null;
        $this->studyProgramName = '';
        $this->institutionId = null;
        $this->themeName = '';
        $this->themesFocusAreaId = null;
        $this->topicName = '';
        $this->topicsThemeId = null;
        $this->institutionName = '';
        $this->editingId = null;
    }

    public function closeModal(): void
    {
        $this->dispatch('close-modal', modalId: $this->getModalId());
    }

    public function confirmDelete(string $type, int $id, string $name): void
    {
        $this->deletingType = $type;
        $this->deletingId = $id;
        $this->deleteModalTitle = 'Konfirmasi Penghapusan';
        $this->deleteModalMessage = "Apakah Anda yakin ingin menghapus \"{$name}\"?";
    }

    public function closeDeleteModal(): void
    {
        $this->deletingId = null;
        $this->deletingType = '';
        $this->deleteModalTitle = '';
        $this->deleteModalMessage = '';
    }

    private function getModalId(): string
    {
        return 'modal-'.$this->modalFor;
    }

    public function confirmDeleteAction(): void
    {
        if (! $this->deletingId || ! $this->deletingType) {
            return;
        }

        try {
            match ($this->deletingType) {
                'focus-area' => FocusArea::findOrFail($this->deletingId)->delete(),
                'keyword' => Keyword::findOrFail($this->deletingId)->delete(),
                'national-priority' => NationalPriority::findOrFail($this->deletingId)->delete(),
                'partner' => Partner::findOrFail($this->deletingId)->delete(),
                'research-scheme' => ResearchScheme::findOrFail($this->deletingId)->delete(),
                'science-cluster' => ScienceCluster::findOrFail($this->deletingId)->delete(),
                'study-program' => StudyProgram::findOrFail($this->deletingId)->delete(),
                'theme' => Theme::findOrFail($this->deletingId)->delete(),
                'topic' => Topic::findOrFail($this->deletingId)->delete(),
                'institution' => Institution::findOrFail($this->deletingId)->delete(),
                default => null,
            };

            $typeNames = [
                'focus-area' => 'Area Fokus',
                'keyword' => 'Kata Kunci',
                'national-priority' => 'Prioritas Nasional',
                'partner' => 'Mitra',
                'research-scheme' => 'Skema Penelitian',
                'science-cluster' => 'Klaster Sains',
                'study-program' => 'Program Studi',
                'theme' => 'Tema',
                'topic' => 'Topik',
                'institution' => 'Institusi',
            ];

            $typeName = $typeNames[$this->deletingType] ?? 'Data';
            $this->dispatch('notify', message: "{$typeName} berhasil dihapus", type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Gagal menghapus data', type: 'error');
        }

        $this->closeDeleteModal();
    }

    public function save(): void
    {
        match ($this->modalFor) {
            'focus-area' => $this->saveFocusArea(),
            'keyword' => $this->saveKeyword(),
            'national-priority' => $this->saveNationalPriority(),
            'partner' => $this->savePartner(),
            'research-scheme' => $this->saveResearchScheme(),
            'science-cluster' => $this->saveScienceCluster(),
            'study-program' => $this->saveStudyProgram(),
            'theme' => $this->saveTheme(),
            'topic' => $this->saveTopic(),
            'institution' => $this->saveInstitution(),
            default => null,
        };
    }
}
