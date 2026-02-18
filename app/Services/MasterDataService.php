<?php

namespace App\Services;

use App\Models\BudgetComponent;
use App\Models\BudgetGroup;
use App\Models\FocusArea;
use App\Models\MacroResearchGroup;
use App\Models\NationalPriority;
use App\Models\Partner;
use App\Models\ResearchScheme;
use App\Models\ScienceCluster;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\TktLevel;
use App\Models\Topic;
use Illuminate\Support\Collection;

class MasterDataService
{
    protected array $cache = [];

    public function schemes(): Collection
    {
        return $this->cache['schemes'] ??= ResearchScheme::all();
    }

    public function focusAreas(): Collection
    {
        return $this->cache['focus_areas'] ??= FocusArea::with('themes.topics')->get();
    }

    public function themes(?int $focusAreaId = null): Collection
    {
        if ($focusAreaId === null) {
            return $this->cache['themes'] ??= Theme::with('topics')->get();
        }

        return Theme::with('topics')->where('focus_area_id', $focusAreaId)->get();
    }

    public function topics(?int $focusAreaId = null, ?int $themeId = null): Collection
    {
        $query = Topic::query();

        if ($focusAreaId !== null) {
            $query->whereHas('theme', fn ($q) => $q->where('focus_area_id', $focusAreaId));
        }

        if ($themeId !== null) {
            $query->where('theme_id', $themeId);
        }

        return $query->get();
    }

    public function nationalPriorities(): Collection
    {
        return $this->cache['national_priorities'] ??= NationalPriority::all();
    }

    public function scienceClusters(): Collection
    {
        return $this->cache['science_clusters'] ??= ScienceCluster::all();
    }

    public function clusterLevel1Options(): Collection
    {
        return $this->scienceClusters()->whereNull('parent_id');
    }

    public function clusterLevel2Options(?int $level1Id = null): Collection
    {
        $query = $this->scienceClusters()->whereNotNull('parent_id');

        if ($level1Id !== null) {
            $query = $query->where('parent_id', $level1Id);
        }

        // Only include those where parent is level 1 (optional, but good for clarity)
        return $query;
    }

    public function clusterLevel3Options(?int $level2Id = null): Collection
    {
        $query = $this->scienceClusters()->whereNotNull('parent_id');

        if ($level2Id !== null) {
            $query = $query->where('parent_id', $level2Id);
        }

        return $query;
    }

    public function macroResearchGroups(): Collection
    {
        return $this->cache['macro_research_groups'] ??= MacroResearchGroup::all();
    }

    public function partners(): Collection
    {
        return $this->cache['partners'] ??= Partner::all();
    }

    public function budgetGroups(): Collection
    {
        return $this->cache['budget_groups'] ??= BudgetGroup::with('components')->get();
    }

    public function budgetComponents(?int $groupId = null): Collection
    {
        if ($groupId === null) {
            return $this->cache['budget_components'] ??= BudgetComponent::all();
        }

        return BudgetComponent::where('budget_group_id', $groupId)->get();
    }

    public function tktTypes(): Collection
    {
        return $this->cache['tkt_types'] ??= TktLevel::distinct()->pluck('type')->filter();
    }

    public function tktLevelsByType(?string $type = null): Collection
    {
        $query = TktLevel::query();

        if ($type !== null) {
            $query->where('type', $type);
        }

        return $query->orderBy('level')->get();
    }

    public function getTemplateUrl(string $type): ?string
    {
        $key = match ($type) {
            'research' => 'research_proposal_template',
            'community-service' => 'community_service_proposal_template',
            default => null,
        };

        if (! $key) {
            return null;
        }

        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->hasMedia('template')) {
            return $setting->getFirstMediaUrl('template');
        }

        return null;
    }

    public function getApprovalTemplateUrl(string $type): ?string
    {
        $key = match ($type) {
            'research' => 'research_approval_template',
            'community-service' => 'community_service_approval_template',
            default => null,
        };

        if (! $key) {
            return null;
        }

        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->hasMedia('template')) {
            return $setting->getFirstMediaUrl('template');
        }

        return null;
    }

    public function getReportEndorsementTemplateUrl(string $type): ?string
    {
        $key = match ($type) {
            'research' => 'research_report_endorsement_template',
            'community-service' => 'community_service_report_endorsement_template',
            default => null,
        };

        if (! $key) {
            return null;
        }

        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->hasMedia('template')) {
            return $setting->getFirstMediaUrl('template');
        }

        return null;
    }

    public function getPartnerCommitmentTemplateUrl(string $type): ?string
    {
        $key = match ($type) {
            'research' => 'research_partner_commitment_template',
            'community-service' => 'community_service_partner_commitment_template',
            default => null,
        };

        if (! $key) {
            return null;
        }

        $setting = Setting::where('key', $key)->first();

        if ($setting && $setting->hasMedia('template')) {
            return $setting->getFirstMediaUrl('template');
        }

        return null;
    }

    public function clearCache(): void
    {
        $this->cache = [];
    }
}
