<?php

namespace App\Livewire\Forms;

use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\Research;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProposalForm extends Form
{
    public ?Proposal $proposal = null;

    public string $title = '';

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

    #[Validate('nullable|numeric|min:0')]
    public string $sbk_value = '';

    #[Validate('required|integer|min:1|max:10')]
    public string $duration_in_years = '1';

    #[Validate('required|string|min:100')]
    public string $summary = '';

    public string $final_tkt_target = '';

    public string $background = '';

    public string $state_of_the_art = '';

    public string $methodology = '';

    #[Validate('nullable|array')]
    public array $roadmap_data = [];

    // CommunityService-specific fields
    #[Validate('nullable|exists:partners,id')]
    public string $partner_id = '';

    #[Validate('nullable|string|min:50')]
    public string $partner_issue_summary = '';

    #[Validate('nullable|string|min:50')]
    public string $solution_offered = '';

    #[Validate('nullable|array')]
    public array $members = [];

    #[Validate('required')]
    public string $author_tasks = '';

    // Step 2: Substansi Usulan
    #[Validate('nullable|exists:macro_research_groups,id')]
    public string $macro_research_group_id = '';

    public $substance_file;

    #[Validate('nullable|array')]
    public array $outputs = [];

    // Step 3: RAB (Budget)
    #[Validate('nullable|array')]
    public array $budget_items = [];

    // Step 4: Dokumen Pendukung (Partners)
    #[Validate('nullable|array')]
    public array $partner_ids = [];

    public array $new_partner = [
        'name' => '',
        'email' => '',
        'institution' => '',
        'country' => '',
        'address' => '',
    ];

    public $new_partner_commitment_file;

    /**
     * Set proposal data for editing
     */
    public function setProposal(Proposal $proposal): void
    {
        $proposal->load([
            'submitter.identity',
            'detailable',
            'teamMembers.identity',
            'outputs',
            'budgetItems',
            'partners',
            'reviewers.user',
        ]);

        $this->proposal = $proposal;

        // Load common proposal fields
        $this->title = $proposal->title;
        $this->research_scheme_id = $proposal->research_scheme_id ?? '';
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

        // Load detailable-specific fields based on type
        $detailable = $proposal->detailable;

        if ($detailable) {
            if ($detailable instanceof Research) {
                // Research-specific fields
                $this->macro_research_group_id = (string) ($detailable->macro_research_group_id ?? '');
                $this->final_tkt_target = $detailable->final_tkt_target ?? '';
                $this->background = $detailable->background ?? '';
                $this->state_of_the_art = $detailable->state_of_the_art ?? '';
                $this->methodology = $detailable->methodology ?? '';
                $this->roadmap_data = $detailable->roadmap_data ?? [];
                // substance_file is a path string, keep as is
                // $this->substance_file will be null for edit (file uploads handled separately)

                // CommunityService fields should be empty for Research
                $this->partner_id = '';
                $this->partner_issue_summary = '';
                $this->solution_offered = '';
            } elseif ($detailable instanceof CommunityService) {
                // CommunityService-specific fields
                $this->partner_id = $detailable->partner_id ?? '';
                $this->partner_issue_summary = $detailable->partner_issue_summary ?? '';
                $this->solution_offered = $detailable->solution_offered ?? '';
                $this->background = $detailable->background ?? '';
                $this->methodology = $detailable->methodology ?? '';

                // Research fields should be empty for CommunityService
                $this->final_tkt_target = '';
                $this->state_of_the_art = '';
                $this->roadmap_data = [];
            }
        }

        // Load team members (excluding ketua/submitter - only load anggota)
        $this->members = $proposal->teamMembers
            ->filter(function ($member) {
                // Only include non-ketua members (anggota)
                return $member->pivot->role !== 'ketua';
            })
            ->map(function ($member) {
                return [
                    'name' => $member->name,
                    'nidn' => $member->identity?->identity_id,
                    'tugas' => $member->pivot->tasks,
                    'role' => $member->pivot->role,
                    'status' => $member->pivot->status ?? 'pending', // Include status field
                ];
            })
            ->values()
            ->toArray();

        // Load author tasks from ketua's pivot data
        $ketuaMember = $proposal->teamMembers
            ->firstWhere('pivot.role', 'ketua');

        if ($ketuaMember) {
            $this->author_tasks = $ketuaMember->pivot->tasks ?? '';
        }

        // Load outputs
        $this->outputs = $proposal->outputs->map(function ($output) {
            return [
                'year' => $output->output_year,
                'category' => $output->category,
                'type' => $output->type,
                'status' => $output->target_status,
                'description' => '',
            ];
        })->toArray();

        // Load budget items
        $this->budget_items = $proposal->budgetItems->map(function ($item) {
            return [
                'budget_group_id' => $item->budget_group_id,
                'budget_component_id' => $item->budget_component_id,
                'group' => $item->group,
                'component' => $item->component,
                'item' => $item->item_description,
                'unit' => '',
                'volume' => $item->volume,
                'unit_price' => $item->unit_price,
                'total' => $item->total_price,
            ];
        })->toArray();

        // Load partners
        $this->partner_ids = $proposal->partners->pluck('id')->toArray();
    }

    /**
     * Store a new proposal
     */
    public function store(string $submitterId): Proposal
    {
        return DB::transaction(function () use ($submitterId, &$proposal) {
            if ($this->research_scheme_id) {
                // It's a Research proposal
                $proposal = $this->storeResearch($submitterId);
            } else {
                // It's a Community Service proposal
                $proposal = $this->storeCommunityService($submitterId);
            }

            return $proposal;
        });
    }

    /**
     * Store a new Research proposal
     */
    public function storeResearch(string $submitterId): Proposal
    {
        // Do NOT call validate() again - it was already called in the Create component
        // This prevents double-validation and maintains form data integrity

        $research = Research::create([
            'macro_research_group_id' => $this->macro_research_group_id ?: null,
            'final_tkt_target' => $this->final_tkt_target ?: null,
            'background' => $this->background ?: null,
            'state_of_the_art' => $this->state_of_the_art ?: null,
            'methodology' => $this->methodology ?: null,
            'roadmap_data' => $this->roadmap_data ?: null,
            'substance_file' => $this->substance_file ? $this->substance_file->store('substance-files', 'public') : null,
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
            'sbk_value' => $this->sbk_value ?: null,
            'duration_in_years' => (int) $this->duration_in_years,
            'summary' => $this->summary,
            'status' => 'draft',
        ]);

        $this->attachTeamMembers($proposal, $submitterId);
        $this->attachOutputs($proposal);
        $this->attachBudgetItems($proposal);
        $this->attachPartners($proposal);

        return $proposal;
    }

    /**
     * Store a new Community Service proposal
     */
    public function storeCommunityService(string $submitterId): Proposal
    {
        // Do NOT call validate() again - it was already called in the Create component
        // This prevents double-validation and maintains form data integrity

        $communityService = CommunityService::create([
            'partner_id' => $this->partner_id ?: null,
            'partner_issue_summary' => $this->partner_issue_summary ?: null,
            'solution_offered' => $this->solution_offered ?: null,
            'background' => $this->background,
            'methodology' => $this->methodology,
        ]);

        $proposal = Proposal::create([
            'title' => $this->title,
            'submitter_id' => $submitterId,
            'detailable_id' => $communityService->id,
            'detailable_type' => CommunityService::class,
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
        $this->attachOutputs($proposal);
        $this->attachBudgetItems($proposal);
        $this->attachPartners($proposal);

        return $proposal;
    }

    /**
     * Update existing proposal
     */
    public function update(): void
    {
        $this->validate();

        // Update detailable based on type
        $detailable = $this->proposal->detailable;

        if ($detailable) {
            if ($detailable instanceof Research) {
                // Update Research-specific fields
                $detailable->update([
                    'macro_research_group_id' => $this->macro_research_group_id ?: null,
                    'final_tkt_target' => $this->final_tkt_target ?: null,
                    'background' => $this->background,
                    'state_of_the_art' => $this->state_of_the_art ?: null,
                    'methodology' => $this->methodology,
                    'roadmap_data' => $this->roadmap_data ?: null,
                    'substance_file' => $this->substance_file && ! is_string($this->substance_file)
                        ? $this->substance_file->store('substance-files', 'public')
                        : $detailable->substance_file,
                ]);
            } elseif ($detailable instanceof CommunityService) {
                // Update CommunityService-specific fields
                $detailable->update([
                    'partner_id' => $this->partner_id ?: null,
                    'partner_issue_summary' => $this->partner_issue_summary ?: null,
                    'solution_offered' => $this->solution_offered ?: null,
                    'background' => $this->background,
                    'methodology' => $this->methodology,
                ]);
            }
        }

        // Update proposal fields
        $this->proposal->update([
            'title' => $this->title,
            'research_scheme_id' => $this->research_scheme_id ?: null,
            'focus_area_id' => $this->focus_area_id,
            'theme_id' => $this->theme_id,
            'topic_id' => $this->topic_id,
            'national_priority_id' => $this->national_priority_id ?: null,
            'cluster_level1_id' => $this->cluster_level1_id,
            'cluster_level2_id' => $this->cluster_level2_id ?: null,
            'cluster_level3_id' => $this->cluster_level3_id ?: null,
            'sbk_value' => $this->sbk_value ?: null,
            'duration_in_years' => (int) $this->duration_in_years,
            'summary' => $this->summary,
        ]);

        $this->attachTeamMembers($this->proposal, $this->proposal->submitter_id);

        // Update outputs (delete old, create new)
        $this->proposal->outputs()->delete();
        $this->attachOutputs($this->proposal);

        // Update budget items (delete old, create new)
        $this->proposal->budgetItems()->delete();
        $this->attachBudgetItems($this->proposal);

        // Update partners (sync)
        $this->attachPartners($this->proposal);
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
     * Override validation rules based on proposal type
     */
    public function rules(): array
    {
        // Determine if this is a Research or CommunityService proposal
        $isResearch = $this->proposal && $this->proposal->detailable_type === Research::class;
        $isCommunityService = $this->proposal && $this->proposal->detailable_type === CommunityService::class;

        $rules = [
            'title' => 'required|string|max:255',
            'research_scheme_id' => 'nullable|exists:research_schemes,id',
            'focus_area_id' => 'required|exists:focus_areas,id',
            'theme_id' => 'required|exists:themes,id',
            'topic_id' => 'required|exists:topics,id',
            'national_priority_id' => 'nullable|exists:national_priorities,id',
            'cluster_level1_id' => 'required|exists:science_clusters,id',
            'cluster_level2_id' => 'nullable|exists:science_clusters,id',
            'cluster_level3_id' => 'nullable|exists:science_clusters,id',
            'sbk_value' => 'nullable|numeric|min:0',
            'duration_in_years' => 'required|integer|min:1|max:10',
            'summary' => 'required|string|min:100',
        ];

        if ($isCommunityService) {
            $rules['research_scheme_id'] = 'nullable|exists:research_schemes,id';
            $rules['partner_id'] = 'nullable|exists:partners,id';
            $rules['partner_issue_summary'] = 'nullable|string|min:50';
            $rules['solution_offered'] = 'nullable|string|min:50';
        }

        if ($isResearch) {
            $rules['research_scheme_id'] = 'required|exists:research_schemes,id';
        }

        // Add conditional rules based on proposal type
        // if ($isResearch) {
        // $rules['background'] = 'nullable|string|min:200';
        // $rules['methodology'] = 'nullable|string|min:200';
        // $rules['state_of_the_art'] = 'nullable|string|min:200';
        // $rules['final_tkt_target'] = 'nullable|string|max:255';
        // $rules['roadmap_data'] = 'nullable|array';
        // } elseif ($isCommunityService) {
        // For CommunityService, background and methodology can be null or shorter
        // $rules['background'] = 'nullable|string|min:50';
        // $rules['methodology'] = 'nullable|string|min:50';
        // $rules['partner_id'] = 'nullable|exists:partners,id';
        // $rules['partner_issue_summary'] = 'nullable|string|min:50';
        //     $rules['solution_offered'] = 'nullable|string|min:50';
        // } else {
        // For new proposals (no detailable yet), both Research and CommunityService need these
        // $rules['background'] = 'required|string|min:200';
        // $rules['methodology'] = 'required|string|min:200';
        // }

        $rules['members'] = 'nullable|array';

        return $rules;
    }

    private function attachTeamMembers(Proposal $proposal, string $submitterId): void
    {
        // Prepare sync data with all team members
        $syncData = [];

        // Add submitter as ketua (team leader) - always accepted
        $syncData[$submitterId] = [
            'tasks' => $this->author_tasks,
            'role' => 'ketua',
            'status' => 'accepted', // Submitter/ketua is always accepted
        ];

        // Get the submitter user for notifications
        $submitter = \App\Models\User::find($submitterId);

        // Add other team members (anggota) - filter out ketua if it exists in members array
        if (! empty($this->members)) {
            foreach ($this->members as $member) {
                // Skip the ketua (submitter) from the members list
                if (isset($member['role']) && $member['role'] === 'ketua') {
                    continue;
                }

                $identity = \App\Models\Identity::where('identity_id', $member['nidn'])->first();

                if ($identity) {
                    $syncData[$identity->user_id] = [
                        'tasks' => $member['tugas'] ?? '',
                        'role' => 'anggota',
                        'status' => 'pending', // Other team members start as pending
                    ];

                    // Send invitation notification
                    $invitee = $identity->user;
                    $notificationService = app(\App\Services\NotificationService::class);
                    $notificationService->notifyTeamInvitationSent($proposal, $submitter, $invitee);
                }
            }
        }

        // Sync all team members at once - this replaces ALL old members with new sync data
        $proposal->teamMembers()->sync($syncData);
    }

    private function attachOutputs(Proposal $proposal): void
    {
        if (! empty($this->outputs)) {
            foreach ($this->outputs as $output) {
                $proposal->outputs()->create([
                    'output_year' => $output['year'] ?? date('Y'),
                    'category' => $output['category'] ?? '',
                    'type' => $output['type'] ?? '',
                    'target_status' => $output['status'] ?? '',
                ]);
            }
        }
    }

    private function attachBudgetItems(Proposal $proposal): void
    {
        if (! empty($this->budget_items)) {
            foreach ($this->budget_items as $item) {
                $proposal->budgetItems()->create([
                    'budget_group_id' => $item['budget_group_id'] ?? null,
                    'budget_component_id' => $item['budget_component_id'] ?? null,
                    'group' => $item['group'] ?? '',
                    'component' => $item['component'] ?? '',
                    'item_description' => $item['item'] ?? '',
                    'volume' => $item['volume'] ?? 0,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => $item['total'] ?? 0,
                ]);
            }
        }
    }

    private function attachPartners(Proposal $proposal): void
    {
        if (! empty($this->partner_ids)) {
            $proposal->partners()->sync($this->partner_ids);
        }
    }
}
