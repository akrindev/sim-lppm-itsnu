<?php

namespace Database\Seeders;

use App\Models\Proposal;
use App\Models\ProposalOutput;
use Illuminate\Database\Seeder;

class ProposalOutputSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder is intended to be used to populate outputs for EXISTING proposals
        // or to create master data if we had a master table.
        // Since we don't have a master table for output types yet (it's a string field),
        // we will just demonstrate how to add outputs to a proposal.

        // Example: Add outputs to the first proposal if exists
        $proposal = Proposal::first();

        if ($proposal) {
            // Research Outputs
            if ($proposal->detailable_type === 'research') {
                // Mandatory
                ProposalOutput::create([
                    'proposal_id' => $proposal->id,
                    'output_year' => date('Y'),
                    'category' => 'Wajib',
                    'group' => 'Publikasi',
                    'type' => 'Jurnal Nasional Terakreditasi',
                    'target_status' => 'Published',
                ]);

                // Additional
                ProposalOutput::create([
                    'proposal_id' => $proposal->id,
                    'output_year' => date('Y'),
                    'category' => 'Tambahan',
                    'group' => 'HKI',
                    'type' => 'Paten Sederhana',
                    'target_status' => 'Granted',
                ]);
            }
            // Community Service Outputs
            else {
                // Mandatory
                ProposalOutput::create([
                    'proposal_id' => $proposal->id,
                    'output_year' => date('Y'),
                    'category' => 'Wajib',
                    'group' => 'Media Massa',
                    'type' => 'Publikasi Media Massa',
                    'target_status' => 'Published',
                ]);

                ProposalOutput::create([
                    'proposal_id' => $proposal->id,
                    'output_year' => date('Y'),
                    'category' => 'Wajib',
                    'group' => 'Video',
                    'type' => 'Video Kegiatan',
                    'target_status' => 'Uploaded',
                ]);
            }
        }
    }
}
