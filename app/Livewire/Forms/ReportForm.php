<?php

declare(strict_types=1);

namespace App\Livewire\Forms;

use App\Models\AdditionalOutput;
use App\Models\Keyword;
use App\Models\MandatoryOutput;
use App\Models\ProgressReport;
use App\Models\Proposal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ReportForm extends Form
{
    public ?ProgressReport $progressReport = null;

    public string $summaryUpdate = '';
    public string $keywordsInput = '';
    public int $reportingYear;
    public string $reportingPeriod = 'semester_1';

    public array $mandatoryOutputs = [];
    public array $additionalOutputs = [];

    public ?int $editingMandatoryId = null;
    public ?int $editingAdditionalId = null;

    public array $tempMandatoryFiles = [];
    public array $tempAdditionalFiles = [];

    public function setReport(ProgressReport $report): void
    {
        $this->progressReport = $report;
        $this->summaryUpdate = $report->summary_update ?? '';
        $this->keywordsInput = $report->keywords->pluck('name')->implode(', ');
        $this->reportingYear = (int) $report->reporting_year;
        $this->reportingPeriod = $report->reporting_period;
        $this->mandatoryOutputs = $report->mandatoryOutputs->keyBy('proposal_output_id')->toArray();
        $this->additionalOutputs = $report->additionalOutputs->keyBy('id')->toArray();
    }

    public function rules(): array
    {
        return [
            'summaryUpdate' => ['required', 'string', 'min:10'],
            'keywordsInput' => ['nullable', 'string'],
            'reportingYear' => ['required', 'integer', 'min:2020', 'max:2099'],
            'reportingPeriod' => [
                'required',
                'string',
                Rule::in(['semester_1', 'semester_2', 'annual', 'final']),
            ],
        ];
    }

    public function validateReportData(): void
    {
        $this->validate();
    }

    public function save(Proposal $proposal): ProgressReport
    {
        $this->validateReportData();

        DB::beginTransaction();

        try {
            $report = ProgressReport::updateOrCreate(
                [
                    'id' => $this->progressReport?->id,
                ],
                [
                    'proposal_id' => $proposal->id,
                    'summary_update' => $this->summaryUpdate,
                    'reporting_year' => $this->reportingYear,
                    'reporting_period' => $this->reportingPeriod,
                    'submitted_by' => Auth::id(),
                    'submitted_at' => now(),
                ]
            );

            $this->saveKeywords($report);
            $this->saveOutputs($report);

            DB::commit();

            $this->progressReport = $report;

            return $report;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function saveKeywords(ProgressReport $report): void
    {
        if (empty($this->keywordsInput)) {
            return;
        }

        $keywordNames = array_map('trim', explode(',', $this->keywordsInput));
        $keywords = [];

        foreach ($keywordNames as $name) {
            if (empty($name)) {
                continue;
            }

            $keyword = Keyword::firstOrCreate(['name' => $name]);
            $keywords[] = $keyword->id;
        }

        $report->keywords()->sync($keywords);
    }

    protected function saveOutputs(ProgressReport $report): void
    {
        foreach ($this->mandatoryOutputs as $proposalOutputId => $output) {
            MandatoryOutput::updateOrCreate(
                [
                    'id' => $output['id'] ?? null,
                    'proposal_output_id' => $proposalOutputId,
                ],
                [
                    'description' => $output['description'] ?? '',
                    'status' => $output['status'] ?? 'not_started',
                ]
            );
        }

        foreach ($this->additionalOutputs as $id => $output) {
            if (isset($output['_delete']) && $output['_delete']) {
                if ($id) {
                    AdditionalOutput::where('id', $id)->delete();
                }
                unset($this->additionalOutputs[$id]);
                continue;
            }

            AdditionalOutput::updateOrCreate(
                [
                    'id' => $id ?: null,
                ],
                [
                    'progress_report_id' => $report->id,
                    'description' => $output['description'] ?? '',
                    'status' => $output['status'] ?? 'not_started',
                ]
            );
        }
    }

    public function addAdditionalOutput(): void
    {
        $this->additionalOutputs[] = [
            'id' => null,
            'description' => '',
            'status' => 'not_started',
        ];

        $this->editingAdditionalId = array_key_last($this->additionalOutputs);
    }

    public function removeAdditionalOutput(int $key): void
    {
        if (isset($this->additionalOutputs[$key]['id'])) {
            $this->additionalOutputs[$key]['_delete'] = true;
        } else {
            unset($this->additionalOutputs[$key]);
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'summaryUpdate',
            'keywordsInput',
            'reportingYear',
            'reportingPeriod',
            'mandatoryOutputs',
            'additionalOutputs',
            'editingMandatoryId',
            'editingAdditionalId',
            'tempMandatoryFiles',
            'tempAdditionalFiles',
        ]);

        $this->progressReport = null;
    }
}
