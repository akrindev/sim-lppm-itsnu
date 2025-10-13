<?php

namespace App\Livewire\Reports;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

class Research extends Component
{
    public string $period = '2025';

    /**
     * Update the selected reporting period.
     */
    public function setPeriod(string $period): void
    {
        if (! Arr::exists($this->reportTimeline(), $period)) {
            return;
        }

        $this->period = $period;
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.reports.research', [
            'periods' => array_keys($this->reportTimeline()),
            'summary' => $this->summaryMetrics(),
            'milestones' => $this->milestonesForPeriod($this->period),
        ]);
    }

    /**
     * Aggregate report metrics for the dashboard cards.
     */
    protected function summaryMetrics(): array
    {
        $timeline = collect($this->reportTimeline())->collapse();

        return [
            [
                'label' => __('Proposal Disetujui'),
                'value' => $timeline->where('metric', 'approved')->sum('value'),
                'icon' => 'check',
                'variant' => 'bg-green-lt text-green',
            ],
            [
                'label' => __('Proposal Berjalan'),
                'value' => $timeline->where('metric', 'ongoing')->sum('value'),
                'icon' => 'clock',
                'variant' => 'bg-blue-lt text-blue',
            ],
            [
                'label' => __('Laporan Terkumpul'),
                'value' => $timeline->where('metric', 'submitted_report')->sum('value'),
                'icon' => 'file-text',
                'variant' => 'bg-yellow-lt text-yellow',
            ],
        ];
    }

    /**
     * Retrieve milestones for a specific period.
     */
    protected function milestonesForPeriod(string $period): Collection
    {
        $milestones = collect($this->reportTimeline()[$period] ?? [])
            ->where('type', 'milestone')
            ->sortBy('order');

        return $milestones->values();
    }

    /**
     * Example reporting data.
     */
    protected function reportTimeline(): array
    {
        return [
            '2025' => [
                ['type' => 'milestone', 'order' => 1, 'title' => __('Januari'), 'description' => __('Periode pengumpulan laporan kemajuan gelombang 1.'), 'metric' => 'submitted_report', 'value' => 12],
                ['type' => 'milestone', 'order' => 2, 'title' => __('Mei'), 'description' => __('Evaluasi tengah tahun oleh tim reviewer internal.'), 'metric' => 'ongoing', 'value' => 18],
                ['type' => 'milestone', 'order' => 3, 'title' => __('September'), 'description' => __('Penetapan penerima dana tambahan riset strategis.'), 'metric' => 'approved', 'value' => 7],
            ],
            '2024' => [
                ['type' => 'milestone', 'order' => 1, 'title' => __('Februari'), 'description' => __('Review laporan akhir tahun sebelumnya.'), 'metric' => 'submitted_report', 'value' => 15],
                ['type' => 'milestone', 'order' => 2, 'title' => __('Juni'), 'description' => __('Workshop peningkatan mutu laporan penelitian.'), 'metric' => 'ongoing', 'value' => 10],
                ['type' => 'milestone', 'order' => 3, 'title' => __('Oktober'), 'description' => __('Publikasi hasil penelitian unggulan fakultas.'), 'metric' => 'approved', 'value' => 5],
            ],
        ];
    }
}
