<?php

namespace App\Services;

use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use setasign\Fpdi\Fpdi;

class ProposalPdfService
{
    /**
     * Export the proposal to a combined PDF.
     *
     * @return string Path to the temporary combined PDF file
     */
    public function export(Proposal $proposal): string
    {
        // 1. Generate the basic info PDF using DomPDF
        $infoPdfContent = Pdf::loadView('pdf.proposal-export', [
            'proposal' => $proposal->load([
                'submitter.identity.institution',
                'submitter.identity.studyProgram',
                'teamMembers.identity.institution',
                'teamMembers.identity.studyProgram',
                'researchScheme',
                'focusArea',
                'theme',
                'topic',
                'keywords',
                'budgetItems.budgetGroup',
                'budgetItems.budgetComponent',
                'partners',
                'detailable.macroResearchGroup',
                'outputs',
            ]),
        ])->setPaper('a4', 'portrait')->output();

        $tempInfoPath = tempnam(sys_get_temp_dir(), 'proposal_info_');
        file_put_contents($tempInfoPath, $infoPdfContent);

        // 2. Prepare FPDI for merging
        // We use a custom class to extend Fpdi if needed, but standard Fpdi works with FPDF
        $pdf = new Fpdi;

        // Add pages from the generated info PDF
        $pageCount = $pdf->setSourceFile($tempInfoPath);
        for ($i = 1; $i <= $pageCount; $i++) {
            $templateId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($templateId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }

        // 3. Add pages from the substance file if it exists
        $substanceFile = $proposal->detailable?->getFirstMedia('substance_file');
        if ($substanceFile && file_exists($substanceFile->getPath())) {
            try {
                $substancePageCount = $pdf->setSourceFile($substanceFile->getPath());
                for ($i = 1; $i <= $substancePageCount; $i++) {
                    $templateId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            } catch (\Exception $e) {
                // If PDF version is too high for FPDI, we might fail here.
                // In some environments, we'd use Ghostscript to down-convert.
                // For now, we skip or log.
            }
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'proposal_final_');
        $pdf->Output('F', $outputPath);

        // Cleanup temporary info PDF
        @unlink($tempInfoPath);

        return $outputPath;
    }
}
