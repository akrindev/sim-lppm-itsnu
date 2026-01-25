<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Models\Faculty;
use App\Models\Identity;
use App\Models\Proposal;
use App\Models\ProposalOutput;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $dosen;

    protected Proposal $proposal;

    protected User $dekan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\InstitutionSeeder::class);
        $institution = \App\Models\Institution::first();

        $faculty = Faculty::factory()->create([
            'institution_id' => $institution->id,
        ]);

        $this->dosen = User::factory()->create();
        $this->dosen->assignRole('dosen');
        Identity::factory()->create(['user_id' => $this->dosen->id, 'faculty_id' => $faculty->id]);

        $research = Research::factory()->create();
        $this->proposal = Proposal::factory()->create([
            'submitter_id' => $this->dosen->id,
            'detailable_id' => $research->id,
            'detailable_type' => Research::class,
            'status' => ProposalStatus::COMPLETED,
        ]);

        // Add mandatory output to proposal
        ProposalOutput::factory()->create([
            'proposal_id' => $this->proposal->id,
            'category' => 'Wajib',
            'type' => 'Jurnal Nasional',
        ]);

        $this->dekan = User::factory()->create();
        $this->dekan->assignRole('dekan');
    }

    public function test_dosen_can_create_and_submit_progress_report()
    {
        $this->actingAs($this->dosen);

        $component = Livewire::test(\App\Livewire\Research\ProgressReport\Show::class, [
            'proposal' => $this->proposal,
        ])
            ->set('form.summaryUpdate', 'This is an updated summary for progress report.')
            ->set('form.keywordsInput', 'test; progress; report')
            ->call('save');

        $component->assertHasNoErrors();

        $this->proposal->refresh();
        $report = $this->proposal->progressReports()->where('reporting_period', 'semester_1')->first();

        $this->assertNotNull($report);
        $this->assertEquals('draft', $report->status);

        // Submit the report
        $component->call('submit');
        $component->assertHasNoErrors();

        $this->assertEquals('submitted', $report->fresh()->status);
    }

    public function test_dosen_can_create_and_submit_final_report()
    {
        $this->actingAs($this->dosen);

        $component = Livewire::test(\App\Livewire\Research\FinalReport\Show::class, [
            'proposal' => $this->proposal,
        ])
            ->set('form.summaryUpdate', 'This is the final summary.')
            ->set('form.keywordsInput', 'final; report; research')
            ->call('save');

        $component->assertHasNoErrors();

        $this->proposal->refresh();
        $report = $this->proposal->progressReports()->where('reporting_period', 'final')->first();

        $this->assertNotNull($report);
        $this->assertEquals('draft', $report->status);

        // Submit
        $component->call('submit');
        $component->assertHasNoErrors();
        $this->assertEquals('submitted', $report->fresh()->status);
    }

    public function test_dosen_can_fill_mandatory_output_in_report()
    {
        $this->actingAs($this->dosen);

        $report = \App\Models\ProgressReport::create([
            'proposal_id' => $this->proposal->id,
            'reporting_year' => 2025,
            'reporting_period' => 'semester_1',
            'status' => 'draft',
            'summary_update' => 'Initial summary',
        ]);

        $output = $this->proposal->outputs()->where('category', 'Wajib')->first();

        $component = Livewire::test(\App\Livewire\Research\ProgressReport\Show::class, [
            'proposal' => $this->proposal,
        ])
            ->call('editMandatoryOutput', $output->id)
            ->set("form.mandatoryOutputs.{$output->id}.status_type", 'published')
            ->set("form.mandatoryOutputs.{$output->id}.journal_title", 'Test Journal')
            ->set("form.mandatoryOutputs.{$output->id}.article_title", 'Test Article')
            ->set("form.mandatoryOutputs.{$output->id}.author_status", 'first_author')
            ->set("form.mandatoryOutputs.{$output->id}.publication_year", 2025)
            ->call('saveMandatoryOutput', $output->id);

        $component->assertHasNoErrors();

        $this->proposal->refresh();
        $report = $this->proposal->progressReports()->first();
        $this->assertNotNull($report);

        $mandatoryOutput = $report->mandatoryOutputs()->where('proposal_output_id', $output->id)->first();
        $this->assertNotNull($mandatoryOutput);
        $this->assertEquals('published', $mandatoryOutput->status_type);
    }

    public function test_dosen_can_add_daily_note()
    {
        $this->actingAs($this->dosen);

        $component = Livewire::test(\App\Livewire\Research\DailyNote\Show::class, [
            'proposal' => $this->proposal,
        ])
            ->set('activity_date', now()->format('Y-m-d'))
            ->set('activity_description', 'Doing some research today.')
            ->set('progress_percentage', 10)
            ->call('save');

        $component->assertHasNoErrors();

        $this->proposal->refresh();
        $note = $this->proposal->dailyNotes()->first();

        $this->assertNotNull($note);
        $this->assertEquals('Doing some research today.', $note->activity_description);
        $this->assertEquals(10, $note->progress_percentage);
    }

    public function test_dosen_can_create_community_service_progress_report()
    {
        $this->actingAs($this->dosen);

        $communityService = \App\Models\CommunityService::factory()->create();
        $proposal = Proposal::factory()->create([
            'submitter_id' => $this->dosen->id,
            'detailable_id' => $communityService->id,
            'detailable_type' => \App\Models\CommunityService::class,
            'status' => ProposalStatus::COMPLETED,
        ]);

        $component = Livewire::test(\App\Livewire\Reports\Show::class, [
            'proposal' => $proposal,
            'type' => 'community-service-progress',
        ])
            ->set('form.summaryUpdate', 'Updated PKM summary.')
            ->set('form.keywordsInput', 'pkm; test')
            ->call('save');

        $component->assertHasNoErrors();

        $proposal->refresh();
        $report = $proposal->progressReports()->first();
        $this->assertNotNull($report);
        $this->assertEquals('Updated PKM summary.', $report->summary_update);
        $this->assertNotNull($report);
        $this->assertEquals('Updated PKM summary.', $report->summary_update);
    }

    public function test_dekan_can_view_progress_report()
    {
        $this->actingAs($this->dekan)
            ->withSession(['active_role' => 'dekan']);

        Livewire::test(\App\Livewire\Research\ProgressReport\Show::class, [
            'proposal' => $this->proposal,
        ])
            ->assertStatus(200);
    }

    public function test_dekan_can_view_daily_notes()
    {
        $this->actingAs($this->dekan)
            ->withSession(['active_role' => 'dekan']);

        Livewire::test(\App\Livewire\Research\DailyNote\Show::class, [
            'proposal' => $this->proposal,
        ])
            ->assertStatus(200);
    }
}
