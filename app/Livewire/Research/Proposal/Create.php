<?php

namespace App\Livewire\Research\Proposal;

use App\Models\Proposal;
use App\Models\Research;
use Livewire\Component;

class Create extends Component
{
    public string $title = '';
    public string $research_scheme_id = '';
    public string $focus_area_id = '';
    public string $theme_id = '';
    public string $topic_id = '';
    public string $national_priority_id = '';
    public string $cluster_level1_id = '';
    public string $cluster_level2_id = '';
    public string $cluster_level3_id = '';
    public string $sbk_value = '';
    public string $duration_in_years = '1';
    public string $summary = '';

    // Research specific fields
    public string $final_tkt_target = '';
    public string $background = '';
    public string $state_of_the_art = '';
    public string $methodology = '';
    public array $roadmap_data = [];
    public array $members = [];
    public string $member_nidn = '';
    public string $member_tugas = '';
    public bool $showMemberModal = false;

    /**
     * Get validation rules for the proposal.
     */
    protected function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'research_scheme_id' => 'required|exists:research_schemes,id',
            'focus_area_id' => 'required|exists:focus_areas,id',
            'theme_id' => 'required|exists:themes,id',
            'topic_id' => 'required|exists:topics,id',
            'national_priority_id' => 'nullable|exists:national_priorities,id',
            'cluster_level1_id' => 'required|exists:science_clusters,id',
            'cluster_level2_id' => 'nullable|exists:science_clusters,id',
            'cluster_level3_id' => 'nullable|exists:science_clusters,id',
            'sbk_value' => 'required|numeric|min:0',
            'duration_in_years' => 'required|integer|min:1|max:10',
            'summary' => 'required|string|min:100',
            'final_tkt_target' => 'required|string|max:255',
            'background' => 'required|string|min:200',
            'state_of_the_art' => 'required|string|min:200',
            'methodology' => 'required|string|min:200',
            'roadmap_data' => 'nullable|array',
            'members' => 'nullable|array',
            'members.*.nidn' => 'required_with:members|string|max:255',
            'members.*.tugas' => 'required_with:members|string|max:500',
        ];
    }

    /**
     * Add member
     */
    public function addMember(): void
    {
        $this->validate([
            'member_nidn' => 'required|string|max:255',
            'member_tugas' => 'required|string|max:500',
        ]);

        $this->members[] = [
            'nidn' => $this->member_nidn,
            'tugas' => $this->member_tugas,
        ];

        $this->member_nidn = '';
        $this->member_tugas = '';
        $this->showMemberModal = false;
    }

    /**
     * Remove member
     */
    public function removeMember(int $index): void
    {
        unset($this->members[$index]);
        $this->members = array_values($this->members);
    }

    /**
     * Save the proposal.
     */
    public function save(): void
    {
        $this->validate();

        try {
            $research = Research::create([
                'final_tkt_target' => $this->final_tkt_target,
                'background' => $this->background,
                'state_of_the_art' => $this->state_of_the_art,
                'methodology' => $this->methodology,
                'roadmap_data' => $this->roadmap_data ?: null,
            ]);

            $proposal = Proposal::create([
                'title' => $this->title,
                'submitter_id' => (int) auth()->id(),
                'detailable_id' => $research->id,
                'detailable_type' => Research::class,
                'research_scheme_id' => $this->research_scheme_id,
                'focus_area_id' => $this->focus_area_id,
                'theme_id' => $this->theme_id,
                'topic_id' => $this->topic_id,
                'national_priority_id' => $this->national_priority_id ?: null,
                'cluster_level1_id' => $this->cluster_level1_id,
                'cluster_level2_id' => $this->cluster_level2_id ?: null,
                'cluster_level3_id' => $this->cluster_level3_id ?: null,
                'sbk_value' => $this->sbk_value,
                'duration_in_years' => (int) $this->duration_in_years,
                'summary' => $this->summary,
                'status' => 'draft',
            ]);

            // Store members data with their tasks
            if (!empty($this->members)) {
                foreach ($this->members as $member) {
                    $proposal->proposalMembers()->create([
                        'nidn' => $member['nidn'],
                        'tugas' => $member['tugas'],
                    ]);
                }
            }

            session()->flash('success', 'Proposal penelitian berhasil dibuat');
            $this->redirect(route('research.proposal.show', $proposal));
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal membuat proposal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.research.proposal.create', [
            'schemes' => \App\Models\ResearchScheme::all(),
            'focusAreas' => \App\Models\FocusArea::all(),
            'themes' => \App\Models\Theme::all(),
            'topics' => \App\Models\Topic::all(),
            'nationalPriorities' => \App\Models\NationalPriority::all(),
            'scienceClusters' => \App\Models\ScienceCluster::all(),
        ]);
    }
}
