<?php

namespace App\Services;

use App\Models\Proposal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
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
                'clusterLevel1',
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

        $tempSubstancePath = null;
        $substanceFile = $proposal->detailable?->getFirstMedia('substance_file');
        if ($substanceFile) {
            $substanceSourcePath = $this->resolveReadableMediaPath($substanceFile, $tempSubstancePath);

            if ($substanceSourcePath !== null) {
                try {
                    $substancePageCount = $pdf->setSourceFile($substanceSourcePath);
                    for ($i = 1; $i <= $substancePageCount; $i++) {
                        $templateId = $pdf->importPage($i);
                        $size = $pdf->getTemplateSize($templateId);
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                    }
                } catch (\Exception $exception) {
                    report($exception);
                }
            }
        }

        $outputPath = tempnam(sys_get_temp_dir(), 'proposal_final_');
        $pdf->Output('F', $outputPath);

        // Cleanup temporary info PDF
        @unlink($tempInfoPath);
        if ($tempSubstancePath !== null) {
            @unlink($tempSubstancePath);
        }

        return $outputPath;
    }

    private function resolveReadableMediaPath(object $media, ?string &$tempSubstancePath): ?string
    {
        $driver = strtolower((string) config("filesystems.disks.{$media->disk}.driver", ''));

        if ($driver === 'local') {
            $localPath = $media->getPath();

            return is_file($localPath) ? $localPath : null;
        }

        $relativePath = $media->getPathRelativeToRoot();
        $disk = Storage::disk($media->disk);

        if (! $disk->exists($relativePath)) {
            return null;
        }

        $stream = $disk->readStream($relativePath);
        if (! is_resource($stream)) {
            return null;
        }

        $tempSubstancePath = tempnam(sys_get_temp_dir(), 'proposal_substance_');
        if ($tempSubstancePath === false) {
            fclose($stream);

            return null;
        }

        $tempHandle = fopen($tempSubstancePath, 'wb');
        if (! is_resource($tempHandle)) {
            fclose($stream);
            @unlink($tempSubstancePath);
            $tempSubstancePath = null;

            return null;
        }

        stream_copy_to_stream($stream, $tempHandle);
        fclose($stream);
        fclose($tempHandle);

        return $tempSubstancePath;
    }
}
