<?php

namespace App\Livewire\AdminLppm\Monev;

use App\Livewire\Concerns\HasToast;
use App\Models\Proposal;
use App\Models\ProposalMonev;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class MonevIndex extends Component
{
    use HasToast, WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = 'all';

    public $selectedProposal;

    public $selectedMonev;

    public $monev_date;

    public $progress_percentage = 0;

    public $notes;

    public $berita_acara;

    public $borang;

    public $rekap_penilaian;

    public $showListModal = false;

    public $showFormModal = false;

    public function mount()
    {
        if (! Auth::user()->hasRole('admin lppm')) {
            abort(403);
        }
    }

    public function selectProposal($id)
    {
        $this->selectedProposal = Proposal::with('monevs')->findOrFail($id);
        $this->showListModal = true;
    }

    public function addMonev()
    {
        $this->reset(['selectedMonev', 'monev_date', 'progress_percentage', 'notes', 'berita_acara', 'borang', 'rekap_penilaian']);
        $this->monev_date = now()->format('Y-m-d');
        $this->progress_percentage = 0;
        $this->showFormModal = true;
    }

    public function editMonev($id)
    {
        $this->selectedMonev = ProposalMonev::findOrFail($id);
        $this->monev_date = $this->selectedMonev->monev_date->format('Y-m-d');
        $this->progress_percentage = $this->selectedMonev->progress_percentage;
        $this->notes = $this->selectedMonev->notes;
        $this->showFormModal = true;
    }

    public function saveMonev()
    {
        $isNew = ! $this->selectedMonev;

        $this->validate([
            'monev_date' => 'required|date',
            'progress_percentage' => 'required|integer|min:0|max:100',
            'notes' => 'nullable|string',
            'berita_acara' => [
                $isNew || ! $this->selectedMonev?->hasMedia('berita_acara') ? 'required' : 'nullable',
                'file', 'mimes:pdf,doc,docx', 'max:10240',
            ],
            'borang' => [
                $isNew || ! $this->selectedMonev?->hasMedia('borang') ? 'required' : 'nullable',
                'file', 'mimes:pdf,doc,docx', 'max:10240',
            ],
            'rekap_penilaian' => [
                $isNew || ! $this->selectedMonev?->hasMedia('rekap_penilaian') ? 'required' : 'nullable',
                'file', 'mimes:pdf,doc,docx', 'max:10240',
            ],
        ], [
            'berita_acara.required' => 'File Berita Acara wajib diunggah.',
            'borang.required' => 'File Borang Monev wajib diunggah.',
            'rekap_penilaian.required' => 'File Rekap Penilaian wajib diunggah.',
        ]);

        $monev = $this->selectedMonev ?? new ProposalMonev(['proposal_id' => $this->selectedProposal->id]);
        $monev->monev_date = $this->monev_date;
        $monev->progress_percentage = $this->progress_percentage;
        $monev->notes = $this->notes;
        $monev->save();

        if ($this->berita_acara) {
            $monev->clearMediaCollection('berita_acara');
            $monev->addMedia($this->berita_acara->getRealPath())
                ->usingFileName($this->berita_acara->getClientOriginalName())
                ->toMediaCollection('berita_acara');
        }

        if ($this->borang) {
            $monev->clearMediaCollection('borang');
            $monev->addMedia($this->borang->getRealPath())
                ->usingFileName($this->borang->getClientOriginalName())
                ->toMediaCollection('borang');
        }

        if ($this->rekap_penilaian) {
            $monev->clearMediaCollection('rekap_penilaian');
            $monev->addMedia($this->rekap_penilaian->getRealPath())
                ->usingFileName($this->rekap_penilaian->getClientOriginalName())
                ->toMediaCollection('rekap_penilaian');
        }

        $this->toastSuccess('Data Monev berhasil disimpan.');
        $this->selectedProposal->load('monevs');
        $this->reset(['showFormModal', 'berita_acara', 'borang', 'rekap_penilaian']);
    }

    public function deleteMonev($id)
    {
        $monev = ProposalMonev::findOrFail($id);
        $monev->delete();
        $this->toastSuccess('Data Monev berhasil dihapus.');
        $this->selectedProposal->load('monevs');
    }

    public function downloadTemplate($key)
    {
        $setting = Setting::where('key', $key)->first();

        if (! $setting || ! $setting->hasMedia('template')) {
            $this->toastError('Template belum tersedia.');

            return null;
        }

        $media = $setting->getFirstMedia('template');
        $relativePath = $media->getPathRelativeToRoot();

        if (! Storage::disk($media->disk)->exists($relativePath)) {
            $this->toastError('Template tidak ditemukan di penyimpanan.');

            return null;
        }

        return $media->toResponse(request());
    }

    #[Computed]
    public function proposals()
    {
        return Proposal::query()
            ->where('status', \App\Enums\ProposalStatus::COMPLETED)
            ->with(['submitter', 'detailable', 'monevs'])
            ->when($this->search, function ($query) {
                $query->where('title', 'like', "%{$this->search}%")
                    ->orWhereHas('submitter', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            })
            ->when($this->typeFilter !== 'all', function ($query) {
                $detailableType = $this->typeFilter === 'research'
                    ? \App\Models\Research::class
                    : \App\Models\CommunityService::class;
                $query->where('detailable_type', $detailableType);
            })
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.admin-lppm.monev.monev-index');
    }
}
