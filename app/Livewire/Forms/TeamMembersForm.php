<?php

namespace App\Livewire\Forms;

use App\Models\Identity;
use Livewire\Attributes\Computed;
use Livewire\Component;

class TeamMembersForm extends Component
{
    public array $members = [];

    public string $member_nidn = '';

    public string $member_tugas = '';

    public bool $memberFound = false;

    public ?array $foundMember = null;

    public string $modalTitle = 'Tambah Anggota Peneliti';

    public string $memberLabel = 'Anggota Peneliti';

    /**
     * Mount the component with optional configuration
     */
    public function mount(?array $members = null, ?string $modalTitle = null, ?string $memberLabel = null): void
    {
        if ($members !== null) {
            $this->members = $members;
        }

        if ($modalTitle !== null) {
            $this->modalTitle = $modalTitle;
        }

        if ($memberLabel !== null) {
            $this->memberLabel = $memberLabel;
        }
    }

    /**
     * Check if member identity exists in system
     */
    public function checkMember(): void
    {
        $this->validate([
            'member_nidn' => 'required|string|max:255',
        ]);

        // Search for user by identity_id (NIDN/NIP)
        $identity = Identity::where('identity_id', $this->member_nidn)
            ->with('user', 'institution', 'studyProgram')
            ->first();

        // if it self show error
        if ($identity && request()->user() && $identity->user_id === request()->user()->getKey()) {
            $this->memberFound = false;
            $this->foundMember = null;
            $this->addError('member_nidn', 'Anda tidak dapat menambahkan diri sendiri sebagai anggota');

            return;
        }

        if ($identity) {
            $this->memberFound = true;
            $this->foundMember = [
                'name' => $identity->user->name,
                'email' => $identity->user->email,
                'nidn' => $identity->identity_id,
                'institution' => $identity->institution?->name,
                'study_program' => $identity->studyProgram?->name,
                'identity_type' => $identity->type,
                'sinta_id' => $identity->sinta_id,
            ];
        } else {
            $this->memberFound = false;
            $this->foundMember = null;
            $this->addError('member_nidn', 'NIDN/NIP tidak ditemukan dalam sistem');
        }
    }

    /**
     * Add member to the list
     */
    public function addMember(): void
    {
        $this->validate([
            'member_nidn' => 'required|string|max:255',
            'member_tugas' => 'required|string|max:500',
        ]);

        if (! $this->memberFound || ! $this->foundMember) {
            $this->addError('member_nidn', 'Silakan cek anggota terlebih dahulu');

            return;
        }

        // Check if member already added
        $alreadyAdded = collect($this->members)->contains(function ($member) {
            return $member['nidn'] === $this->member_nidn;
        });

        if ($alreadyAdded) {
            $this->addError('member_nidn', 'Anggota ini sudah ditambahkan');

            return;
        }

        $this->members[] = [
            'name' => $this->foundMember['name'],
            'nidn' => $this->member_nidn,
            'tugas' => $this->member_tugas,
            'status' => 'pending',
            'sinta_id' => $this->foundMember['sinta_id'] ?? null,
        ];

        $this->resetMemberForm();

        $this->dispatch('members-updated', members: $this->members);

        // Dispatch event to close modal
        $this->dispatch('close-modal', modalId: 'modal-add-member');
    }

    /**
     * Remove member from the list
     */
    public function removeMember(int $index): void
    {
        unset($this->members[$index]);
        $this->members = array_values($this->members);

        // Dispatch to parent component
        $this->dispatch('members-updated', members: $this->members);
    }

    /**
     * Reset member form
     */
    public function resetMemberForm(): void
    {
        $this->member_nidn = '';
        $this->member_tugas = '';
        $this->memberFound = false;
        $this->foundMember = null;
        $this->resetErrorBag();
    }

    /**
     * Get members data
     */
    #[Computed]
    public function membersList(): array
    {
        return $this->members;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.forms.team-members-form');
    }
}
