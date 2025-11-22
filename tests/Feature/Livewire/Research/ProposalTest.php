<?php

namespace Tests\Feature\Livewire\Research;

use App\Livewire\Research\Proposal\Create;
use App\Models\FocusArea;
use App\Models\Identity;
use App\Models\MacroResearchGroup;
use App\Models\ResearchScheme;
use App\Models\ScienceCluster;
use App\Models\Theme;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProposalTest extends TestCase
{
    use RefreshDatabase;

    public function test_step_1_validation_requires_members()
    {
        $user = User::factory()->create();
        $identity = Identity::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $scheme = ResearchScheme::factory()->create();
        $focusArea = FocusArea::factory()->create();
        $theme = Theme::factory()->create();
        $topic = Topic::factory()->create();
        $cluster = ScienceCluster::factory()->create(['level' => 1]);

        Livewire::test(Create::class)
            ->set('form.title', 'Test Proposal')
            ->set('form.research_scheme_id', $scheme->id)
            ->set('form.duration_in_years', 1)
            ->set('form.focus_area_id', $focusArea->id)
            ->set('form.theme_id', $theme->id)
            ->set('form.topic_id', $topic->id)
            ->set('form.cluster_level1_id', $cluster->id)
            ->set('form.summary', str_repeat('a', 100))
            ->set('form.author_tasks', 'Test Tasks')
            ->set('form.members', []) // No members
            ->call('nextStep')
            ->assertHasErrors(['form.members']);
    }

    public function test_step_2_validation_requires_macro_group_file_and_outputs()
    {
        $user = User::factory()->create();
        $identity = Identity::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $macroGroup = MacroResearchGroup::create(['name' => 'Test Group']);

        Livewire::test(Create::class)
            ->set('currentStep', 2)
            ->set('form.macro_research_group_id', '')
            ->set('form.substance_file', null)
            ->set('form.outputs', [])
            ->call('nextStep')
            ->assertHasErrors([
                'form.macro_research_group_id',
                'form.substance_file',
                'form.outputs'
            ]);
    }

    public function test_step_2_validation_requires_wajib_output()
    {
        $user = User::factory()->create();
        $identity = Identity::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $macroGroup = MacroResearchGroup::create(['name' => 'Test Group']);
        Storage::fake('local');
        $file = UploadedFile::fake()->create('substance.pdf', 1000, 'application/pdf');

        Livewire::test(Create::class)
            ->set('currentStep', 2)
            ->set('form.macro_research_group_id', $macroGroup->id)
            ->set('form.substance_file', $file)
            ->set('form.outputs', [
                ['category' => 'Tambahan', 'group' => 'A', 'type' => 'B', 'status' => 'C', 'description' => 'D']
            ])
            ->call('nextStep')
            ->assertHasErrors(['form.outputs']);
    }

    public function test_step_3_validation_requires_budget_items()
    {
        $user = User::factory()->create();
        $identity = Identity::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(Create::class)
            ->set('currentStep', 3)
            ->set('form.budget_items', [])
            ->call('nextStep')
            ->assertHasErrors(['form.budget_items']);
    }
}
