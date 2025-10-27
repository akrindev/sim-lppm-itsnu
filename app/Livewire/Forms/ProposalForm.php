<?php

namespace App\Livewire\Forms;

use App\Models\Proposal;
use App\Models\Research;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProposalForm extends Form
{
    public ?Proposal $proposal = null;

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|exists:research_schemes,id')]
    public string $research_scheme_id = '';

    #[Validate('required|exists:focus_areas,id')]
    public string $focus_area_id = '';

    #[Validate('required|exists:themes,id')]
    public string $theme_id = '';

    #[Validate('required|exists:topics,id')]
    public string $topic_id = '';

    #[Validate('nullable|exists:national_priorities,id')]
    public string $national_priority_id = '';

    #[Validate('required|exists:science_clusters,id')]
    public string $cluster_level1_id = '';

    #[Validate('nullable|exists:science_clusters,id')]
    public string $cluster_level2_id = '';

    #[Validate('nullable|exists:science_clusters,id')]
    public string $cluster_level3_id = '';

    #[Validate('required|numeric|min:0')]
    public string $sbk_value = '';

    #[Validate('required|integer|min:1|max:10')]
    public string $duration_in_years = '1';

    #[Validate('required|string|min:100')]
    public string $summary = '';

    #[Validate('required|string|max:255')]
    public string $final_tkt_target = '';

    #[Validate('required|string|min:200')]
    public string $background = '';

    #[Validate('required|string|min:200')]
    public string $state_of_the_art = '';

    #[Validate('required|string|min:200')]
    public string $methodology = '';

    #[Validate('nullable|array')]
    public array $roadmap_data = [];

    #[Validate('nullable|array')]
    public array $members = [];

    /**
     * Set proposal data for editing
     */
    public function setProposal(Proposal $proposal): void
    {
        $this->proposal = $proposal;
        $research = $proposal->detailable;

        $this->title = $proposal->title;
        $this->research_scheme_id = $proposal->research_scheme_id;
        $this->focus_area_id = $proposal->focus_area_id;
        $this->theme_id = $proposal->theme_id;
        $this->topic_id = $proposal->topic_id;
        $this->national_priority_id = $proposal->national_priority_id ?? '';
        $this->cluster_level1_id = $proposal->cluster_level1_id;
        $this->cluster_level2_id = $proposal->cluster_level2_id ?? '';
        $this->cluster_level3_id = $proposal->cluster_level3_id ?? '';
        $this->sbk_value = (string) $proposal->sbk_value;
        $this->duration_in_years = (string) $proposal->duration_in_years;
        $this->summary = $proposal->summary;

        if ($research) {
            $this->final_tkt_target = $research->final_tkt_target;
            $this->background = $research->background;
            $this->state_of_the_art = $research->state_of_the_art;
            $this->methodology = $research->methodology;
            $this->roadmap_data = $research->roadmap_data ?? [];
        }

        // Load team members (including ketua/submitter)
        $this->members = $proposal->teamMembers()
            ->with('identity')
            ->get()
            ->map(function ($member) {
                return [
                    'name' => $member->name,
                    'nidn' => $member->identity?->identity_id,
                    'tugas' => $member->pivot->tasks,
                    'role' => $member->pivot->role,
                ];
            })
            ->toArray();
    }

    /**
     * Store a new proposal
     */
    public function store(string $submitterId): Proposal
    {
        $this->validate();

        $research = Research::create([
            'final_tkt_target' => $this->final_tkt_target,
            'background' => $this->background,
            'state_of_the_art' => $this->state_of_the_art,
            'methodology' => $this->methodology,
            'roadmap_data' => $this->roadmap_data ?: null,
        ]);

        $proposal = Proposal::create([
            'title' => $this->title,
            'submitter_id' => $submitterId,
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

        $this->attachTeamMembers($proposal, $submitterId);

        return $proposal;
    }

    /**
     * Update existing proposal
     */
    public function update(): void
    {
        $this->validate();

        $research = $this->proposal->detailable;
        $research->update([
            'final_tkt_target' => $this->final_tkt_target,
            'background' => $this->background,
            'state_of_the_art' => $this->state_of_the_art,
            'methodology' => $this->methodology,
            'roadmap_data' => $this->roadmap_data ?: null,
        ]);

        $this->proposal->update([
            'title' => $this->title,
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
        ]);

        $this->attachTeamMembers($this->proposal, $this->proposal->submitter_id);
    }

    /**
     * Delete proposal and its research
     */
    public function delete(): void
    {
        if ($this->proposal) {
            $this->proposal->teamMembers()->detach();
            $this->proposal->detailable?->delete();
            $this->proposal->delete();
        }
    }

    /**
     * Attach team members to proposal
     */
    private function attachTeamMembers(Proposal $proposal, string $submitterId): void
    {
        // Detach all members first
        $proposal->teamMembers()->detach();

        // Attach non-ketua members
        if (! empty($this->members)) {
            foreach ($this->members as $member) {
                $identity = \App\Models\Identity::where('identity_id', $member['nidn'])->first();

                if ($identity) {
                    $proposal->teamMembers()->attach($identity->user_id, [
                        'tasks' => $member['tugas'],
                        'role' => 'anggota',
                    ]);
                }
            }
        }

        // Attach submitter as team leader (ketua)
        $proposal->teamMembers()->attach($submitterId, [
            'tasks' => 'Peneliti Utama',
            'role' => 'ketua',
        ]);
    }
}
