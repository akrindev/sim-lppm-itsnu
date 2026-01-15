<?php

namespace App\Livewire\Forms;

use App\Models\CommunityService;
use App\Models\Proposal;
use App\Models\Research;
use App\Models\ResearchScheme;
use Illuminate\Support\Facades\Auth;
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

    #[Validate('required|integer|min:2020|max:2050')]
    public string $start_year = '';

    #[Validate('required|string|min:100')]
    public string $summary = '';

    public string $tkt_type = '';

    public array $tkt_results = []; // [level_id => ['percentage' => 100]]

    public array $tkt_indicator_scores = []; // [indicator_id => score]

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

    // Budget validation errors
    public array $budgetValidationErrors = [];

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
        $this->start_year = (string) ($proposal->start_year ?? date('Y'));
        $this->summary = $proposal->summary;

        // Load detailable-specific fields based on type
        $detailable = $proposal->detailable;

        if ($detailable) {
            if ($detailable instanceof Research) {
                // Research-specific fields
                $this->macro_research_group_id = (string) ($detailable->macro_research_group_id ?? '');
                $this->macro_research_group_id = (string) ($detailable->macro_research_group_id ?? '');
                $this->tkt_type = $detailable->tkt_type ?? '';
                // Load TKT results from pivot
                $this->tkt_results = $detailable->tktLevels->mapWithKeys(function ($level) {
                    return [$level->id => ['percentage' => $level->pivot->percentage]];
                })->toArray();
                // Load TKT indicator scores
                $this->tkt_indicator_scores = $detailable->tktIndicators->mapWithKeys(function ($indicator) {
                    return [$indicator->id => $indicator->pivot->score];
                })->toArray();
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
                $this->tkt_type = '';
                $this->tkt_results = [];
                $this->tkt_indicator_scores = [];
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
                'group' => strtolower($output->group ?? ''),
                'type' => $output->type,
                'status' => $output->target_status,
                'description' => $output->description ?? '',
            ];
        })->toArray();

        // Load budget items
        $this->budget_items = $proposal->budgetItems->map(function ($item) {
            return [
                'year' => $item->year ?? 1,
                'budget_group_id' => $item->budget_group_id,
                'budget_component_id' => $item->budget_component_id,
                'group' => $item->group,
                'component' => $item->component,
                'item' => $item->item_description,
                'unit' => $item->budgetComponent?->unit ?? '',
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
            'tkt_type' => $this->tkt_type ?: null,
            'background' => $this->background ?: null,
            'state_of_the_art' => $this->state_of_the_art ?: null,
            'methodology' => $this->methodology ?: null,
            'roadmap_data' => $this->roadmap_data ?: null,
        ]);

        // Upload substance file using Media Library
        if ($this->substance_file) {
            $research
                ->addMedia($this->substance_file->getRealPath())
                ->usingName($this->substance_file->getClientOriginalName())
                ->usingFileName($this->substance_file->hashName())
                ->withCustomProperties(['uploaded_by' => $submitterId])
                ->toMediaCollection('substance_file');

            // Reset to prevent "UnableToRetrieveMetadata" error on subsequent validations
            $this->substance_file = null;
        }

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
            'sbk_value' => ! empty($this->sbk_value) ? $this->sbk_value : null,
            'duration_in_years' => (int) $this->duration_in_years,
            'start_year' => (int) $this->start_year,
            'summary' => $this->summary,
            'status' => 'draft',
        ]);

        $this->attachTeamMembers($proposal, $submitterId);
        $this->attachOutputs($proposal);
        $this->attachBudgetItems($proposal);
        $this->attachPartners($proposal);

        // Attach TKT Levels
        if (! empty($this->tkt_results)) {
            $research->tktLevels()->sync($this->tkt_results);
        }

        // Attach TKT Indicators
        if (! empty($this->tkt_indicator_scores)) {
            $indicatorSyncData = [];
            foreach ($this->tkt_indicator_scores as $indicatorId => $score) {
                $indicatorSyncData[$indicatorId] = ['score' => $score];
            }
            $research->tktIndicators()->sync($indicatorSyncData);
        }

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

        // Upload substance file using Media Library
        if ($this->substance_file) {
            $communityService
                ->addMedia($this->substance_file->getRealPath())
                ->usingName($this->substance_file->getClientOriginalName())
                ->usingFileName($this->substance_file->hashName())
                ->withCustomProperties(['uploaded_by' => $submitterId])
                ->toMediaCollection('substance_file');

            // Reset to prevent "UnableToRetrieveMetadata" error on subsequent validations
            $this->substance_file = null;
        }

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
            'sbk_value' => ! empty($this->sbk_value) ? $this->sbk_value : null,
            'duration_in_years' => (int) $this->duration_in_years,
            'start_year' => (int) $this->start_year,
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
    public function update(bool $validate = true): void
    {
        if ($validate) {
            $this->validate();
        }

        DB::transaction(function (): void {
            // Update detailable based on type
            $detailable = $this->proposal->detailable;

            if ($detailable) {
                if ($detailable instanceof Research) {
                    // Update Research-specific fields
                    $detailable->update([
                        'macro_research_group_id' => $this->macro_research_group_id ?: null,
                        'tkt_type' => $this->tkt_type ?: null,
                        'background' => $this->background,
                        'state_of_the_art' => $this->state_of_the_art ?: null,
                        'methodology' => $this->methodology,
                        'roadmap_data' => $this->roadmap_data ?: null,
                    ]);

                    // Update substance file ONLY if a new file is uploaded
                    if ($this->substance_file && ! is_string($this->substance_file)) {
                        $detailable
                            ->addMedia($this->substance_file->getRealPath())
                            ->usingName($this->substance_file->getClientOriginalName())
                            ->usingFileName($this->substance_file->hashName())
                            ->withCustomProperties(['uploaded_by' => Auth::id()])
                            ->toMediaCollection('substance_file');

                        // Reset to prevent "UnableToRetrieveMetadata" error on subsequent validations
                        $this->substance_file = null;
                    }
                    // IMPORTANT: If $this->substance_file is null, we do NOTHING.
                    // This preserves the existing file in the media collection.

                    // Sync TKT Levels
                    if (! empty($this->tkt_results)) {
                        $detailable->tktLevels()->sync($this->tkt_results);
                    }

                    // Sync TKT Indicators
                    if (! empty($this->tkt_indicator_scores)) {
                        $indicatorSyncData = [];
                        foreach ($this->tkt_indicator_scores as $indicatorId => $score) {
                            $indicatorSyncData[$indicatorId] = ['score' => $score];
                        }
                        $detailable->tktIndicators()->sync($indicatorSyncData);
                    }
                } elseif ($detailable instanceof CommunityService) {
                    // Update CommunityService-specific fields
                    $detailable->update([
                        'partner_id' => $this->partner_id ?: null,
                        'partner_issue_summary' => $this->partner_issue_summary ?: null,
                        'solution_offered' => $this->solution_offered ?: null,
                        'background' => $this->background,
                        'methodology' => $this->methodology,
                    ]);

                    // Update substance file ONLY if a new file is uploaded
                    if ($this->substance_file && ! is_string($this->substance_file)) {
                        $detailable
                            ->addMedia($this->substance_file->getRealPath())
                            ->usingName($this->substance_file->getClientOriginalName())
                            ->usingFileName($this->substance_file->hashName())
                            ->withCustomProperties(['uploaded_by' => Auth::id()])
                            ->toMediaCollection('substance_file');

                        // Reset to prevent "UnableToRetrieveMetadata" error on subsequent validations
                        $this->substance_file = null;
                    }
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
                'sbk_value' => ! empty($this->sbk_value) ? $this->sbk_value : null,
                'duration_in_years' => (int) $this->duration_in_years,
                'start_year' => (int) $this->start_year,
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
        });
    }

    /**
     * Delete proposal and its research
     */
    public function delete(): void
    {
        if ($this->proposal) {
            DB::transaction(function (): void {
                $this->proposal->teamMembers()->detach();
                $this->proposal->detailable?->delete();
                $this->proposal->delete();
            });
        }
    }

    /**
     * Override validation rules based on proposal type
     */
    public function rules(): array
    {
        // Determine if this is a Research or CommunityService proposal
        $isResearch = ($this->proposal && $this->proposal->detailable_type === Research::class) || $this->research_scheme_id;
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
            'start_year' => 'required|integer|min:2020|max:2050',
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
            $rules['tkt_type'] = 'required|string|max:255';
        }

        // Add conditional rules based on proposal type
        // if ($isResearch) {
        // $rules['background'] = 'nullable|string|min:200';
        // $rules['methodology'] = 'nullable|string|min:200';
        // $rules['state_of_the_art'] = 'nullable|string|min:200';
        // $rules['tkt_type'] = 'nullable|string|max:255';
        // $rules['roadmap_data'] = 'nullable|array';

        $rules['tkt_results'] = ['nullable', 'array', function ($attribute, $value, $fail) {
            if (empty($value)) {
                return;
            }

            // 1. Calculate achieved level
            $achievedLevel = 0;
            // Get level models to map IDs to integer levels
            $levels = \App\Models\TktLevel::whereIn('id', array_keys($value))->get();

            foreach ($levels as $level) {
                $data = $value[$level->id] ?? null;
                // Check if passed (percentage >= 80)
                if ($data && isset($data['percentage']) && $data['percentage'] >= 80) {
                    $achievedLevel = max($achievedLevel, $level->level);
                }
            }

            // 2. Get required range for the scheme if selected
            if ($this->research_scheme_id) {
                $scheme = ResearchScheme::find($this->research_scheme_id);
                if ($scheme && $scheme->strata) {
                    $range = \App\Livewire\Research\Proposal\Components\TktMeasurement::getTktRangeForStrata($scheme->strata);

                    // If range exists (not PKM), validate
                    if ($range) {
                        [$min, $max] = $range;

                        // Check if achieved level is within range
                        if ($achievedLevel < $min || $achievedLevel > $max) {
                            $fail("TKT Saat Ini (Level $achievedLevel) tidak sesuai dengan Skema $scheme->strata (Target: Level $min - $max).");
                        }
                    }
                }
            }
        }];
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
                    'output_year' => $output['year'] ?? 1, // date('Y'),
                    'category' => $output['category'] ?? 'Wajib',
                    'group' => $output['group'] ?? '',
                    'type' => $output['type'] ?? '',
                    'target_status' => $output['status'] ?? '',
                ]);
            }
        }
    }

    private function attachBudgetItems(Proposal $proposal): void
    {
        if (! empty($this->budget_items)) {
            // Validate budget before saving
            $this->validateBudgetGroupPercentages();
            $this->validateBudgetCap($this->getProposalType());

            foreach ($this->budget_items as $item) {
                $proposal->budgetItems()->create([
                    'year' => $item['year'] ?? 1,
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

    /**
     * Validate budget items against budget group percentage limits.
     * Percentages are calculated based on the budget cap, not the total budget entered.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateBudgetGroupPercentages(): void
    {
        if (empty($this->budget_items)) {
            return;
        }

        // Get proposal type to determine which budget cap to use
        $proposalType = $this->getProposalType();
        $currentYear = (int) date('Y');

        // Get budget cap for current year and proposal type
        $budgetCap = \App\Models\BudgetCap::getCapForYear($currentYear, $proposalType);

        if ($budgetCap === null || $budgetCap <= 0) {
            // No budget cap set, cannot validate percentages
            throw \Illuminate\Validation\ValidationException::withMessages([
                'budget_items' => [
                    sprintf(
                        'Batas anggaran untuk %s tahun %s belum diatur. Silakan hubungi Admin LPPM.',
                        $proposalType === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat',
                        $currentYear
                    ),
                ],
            ]);
        }

        // Group budget items by budget_group_id and check percentages
        $budgetGroups = \App\Models\BudgetGroup::whereNotNull('percentage')->get();
        $errors = [];

        foreach ($budgetGroups as $group) {
            // Calculate total spent in this group
            $groupTotal = collect($this->budget_items)
                ->where('budget_group_id', $group->id)
                ->sum(fn ($item) => (float) ($item['total'] ?? 0));

            // Calculate percentage used BASED ON BUDGET CAP
            $percentageUsed = ($groupTotal / $budgetCap) * 100;
            $allowedPercentage = (float) $group->percentage;

            // Check if percentage exceeds limit
            if ($percentageUsed > $allowedPercentage) {
                $errors[] = sprintf(
                    'Kelompok anggaran "%s" melebihi batas %s%%. Saat ini: %s%% (Rp %s dari batas anggaran Rp %s)',
                    $group->name,
                    number_format($allowedPercentage, 2),
                    number_format($percentageUsed, 2),
                    number_format($groupTotal, 0, ',', '.'),
                    number_format($budgetCap, 0, ',', '.')
                );
            }
        }

        if (! empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'budget_items' => $errors,
            ]);
        }
    }

    /**
     * Validate total budget against year-based budget cap.
     *
     * @param  string  $proposalType  'research' or 'community_service'
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateBudgetCap(string $proposalType): void
    {
        if (empty($this->budget_items)) {
            return;
        }

        // Calculate total budget
        $totalBudget = collect($this->budget_items)->sum(fn ($item) => (float) ($item['total'] ?? 0));

        if ($totalBudget <= 0) {
            return;
        }

        // Get current year
        $currentYear = (int) date('Y');

        // Get budget cap for current year and proposal type
        $budgetCap = \App\Models\BudgetCap::getCapForYear($currentYear, $proposalType);

        if ($budgetCap === null) {
            // No cap set, allow any amount
            return;
        }

        if ($totalBudget > $budgetCap) {
            $typeLabel = $proposalType === 'research' ? 'Penelitian' : 'Pengabdian Masyarakat';
            throw \Illuminate\Validation\ValidationException::withMessages([
                'budget_items' => [
                    sprintf(
                        'Total anggaran melebihi batas maksimal untuk %s tahun %s. Batas: Rp %s, Total saat ini: Rp %s',
                        $typeLabel,
                        $currentYear,
                        number_format($budgetCap, 0, ',', '.'),
                        number_format($totalBudget, 0, ',', '.')
                    ),
                ],
            ]);
        }
    }

    /**
     * Get proposal type from the current form state.
     */
    private function getProposalType(): string
    {
        return $this->research_scheme_id ? 'research' : 'community_service';
    }
}
