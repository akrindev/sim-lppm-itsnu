<?php

namespace Tests\Feature\Livewire\Research;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_dosen_can_access_research_proposal_create_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('dosen');

        $this->actingAs($user)
            ->get(route('research.proposal.create'))
            ->assertOk();
    }

    public function test_non_dosen_cannot_access_research_proposal_create_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('reviewer');

        $this->actingAs($user)
            ->get(route('research.proposal.create'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_research_proposal_create_page(): void
    {
        $this->get(route('research.proposal.create'))
            ->assertRedirect(route('login'));
    }

    public function test_dosen_route_name_resolves_to_research_create_component_path(): void
    {
        $route = app('router')->getRoutes()->getByName('research.proposal.create');

        $this->assertNotNull($route);
        $this->assertSame('research/proposal/create', $route->uri());
    }
}
