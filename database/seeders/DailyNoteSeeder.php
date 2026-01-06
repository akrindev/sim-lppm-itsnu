<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DailyNoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $proposals = \App\Models\Proposal::where('status', \App\Enums\ProposalStatus::APPROVED)
            ->orWhere('status', \App\Enums\ProposalStatus::COMPLETED)
            ->get();

        foreach ($proposals as $proposal) {
            \App\Models\DailyNote::factory(rand(5, 15))->create([
                'proposal_id' => $proposal->id,
            ]);
        }
    }
}
