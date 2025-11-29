<?php

namespace Tests\Feature\Livewire\Research\Proposal\Components;

use App\Livewire\Research\Proposal\Components\TktMeasurement;
use App\Models\TktIndicator;
use App\Models\TktLevel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TktMeasurementTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_calculates_level_average_correctly()
    {
        // Create TKT Level and Indicators
        $level1 = TktLevel::create(['type' => 'Umum', 'level' => 1, 'description' => 'Level 1']);
        $ind1 = TktIndicator::create(['tkt_level_id' => $level1->id, 'code' => '1.1', 'indicator' => 'Ind 1']);
        $ind2 = TktIndicator::create(['tkt_level_id' => $level1->id, 'code' => '1.2', 'indicator' => 'Ind 2']);

        Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum')
            ->set('indicatorScores.' . $ind1->id, 80)
            ->set('indicatorScores.' . $ind2->id, 80)
            ->assertSet('levelAverages.' . $level1->id, 80);

        Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum')
            ->set('indicatorScores.' . $ind1->id, 100)
            ->set('indicatorScores.' . $ind2->id, 60)
            ->assertSet('levelAverages.' . $level1->id, 80); // (100+60)/2 = 80

        Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum')
            ->set('indicatorScores.' . $ind1->id, 100)
            ->set('indicatorScores.' . $ind2->id, 40)
            ->assertSet('levelAverages.' . $level1->id, 70); // (100+40)/2 = 70
    }

    public function test_it_determines_level_passed_status()
    {
        $level1 = TktLevel::create(['type' => 'Umum', 'level' => 1, 'description' => 'Level 1']);
        $ind1 = TktIndicator::create(['tkt_level_id' => $level1->id, 'code' => '1.1', 'indicator' => 'Ind 1']);

        $component = Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum')
            ->set('indicatorScores.' . $ind1->id, 80);

        $this->assertTrue($component->instance()->isLevelPassed($level1->id));

        $component->set('indicatorScores.' . $ind1->id, 60);
        $this->assertFalse($component->instance()->isLevelPassed($level1->id));
    }

    public function test_it_locks_next_level_if_previous_failed()
    {
        $level1 = TktLevel::create(['type' => 'Umum', 'level' => 1, 'description' => 'Level 1']);
        $ind1 = TktIndicator::create(['tkt_level_id' => $level1->id, 'code' => '1.1', 'indicator' => 'Ind 1']);

        $level2 = TktLevel::create(['type' => 'Umum', 'level' => 2, 'description' => 'Level 2']);
        $ind2 = TktIndicator::create(['tkt_level_id' => $level2->id, 'code' => '2.1', 'indicator' => 'Ind 2']);

        $component = Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum');

        // Level 1 failed (default 0), Level 2 should be locked
        $this->assertTrue($component->instance()->isLevelLocked($level2));

        // Pass Level 1
        $component->set('indicatorScores.' . $ind1->id, 80);

        // Level 2 should be unlocked
        $this->assertFalse($component->instance()->isLevelLocked($level2));
    }

    public function test_it_dispatches_switch_level_event_when_level_passed()
    {
        $level1 = TktLevel::create(['type' => 'Umum', 'level' => 1, 'description' => 'Level 1']);
        $ind1 = TktIndicator::create(['tkt_level_id' => $level1->id, 'code' => '1.1', 'indicator' => 'Ind 1']);

        $level2 = TktLevel::create(['type' => 'Umum', 'level' => 2, 'description' => 'Level 2']);
        $ind2 = TktIndicator::create(['tkt_level_id' => $level2->id, 'code' => '2.1', 'indicator' => 'Ind 2']);

        Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum')
            ->set('indicatorScores.' . $ind1->id, 80)
            ->assertDispatched('switch-level', level: 2);
    }

    public function test_save_dispatches_correct_data()
    {
        $level1 = TktLevel::create(['type' => 'Umum', 'level' => 1, 'description' => 'Level 1']);
        $ind1 = TktIndicator::create(['tkt_level_id' => $level1->id, 'code' => '1.1', 'indicator' => 'Ind 1']);

        Livewire::test(TktMeasurement::class)
            ->call('loadLevels', 'Umum')
            ->set('indicatorScores.' . $ind1->id, 80)
            ->call('save')
            ->assertDispatched('tkt-calculated', function ($event, $data) use ($level1, $ind1) {
                return $data['levelResults'][$level1->id]['percentage'] == 80
                    && $data['indicatorScores'][$ind1->id] == 80;
            });
    }
}
