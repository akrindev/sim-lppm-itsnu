<?php

namespace App\Livewire\Users;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app', ['title' => 'Edit User', 'pageTitle' => 'Edit User', 'pageSubtitle' => 'Update the user profile and role assignments'])]
class Edit extends Component
{
    public string $userId;

    public string $name = '';

    public string $email = '';

    public ?string $selectedRole = null;

    public bool $emailVerified = false;

    // Identity fields
    public string $identity_id = '';

    public string $address = '';

    public ?string $birthdate = null;

    public string $birthplace = '';

    public ?string $sinta_id = null;

    public ?string $type = null;

    public ?string $institution_id = null;

    public ?string $study_program_id = null;

    /**
     * Hydrate the component state from the selected user.
     */
    public function mount(User $user): void
    {
        $this->userId = $user->getKey();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRole = $user->roles()->first()?->name;
        $this->emailVerified = $user->hasVerifiedEmail();

        // Load identity data
        if ($user->identity) {
            $this->identity_id = $user->identity->identity_id;
            $this->address = $user->identity->address ?? '';
            $this->birthdate = $user->identity->birthdate?->format('Y-m-d');
            $this->birthplace = $user->identity->birthplace ?? '';
            $this->sinta_id = $user->identity->sinta_id ?? '';
            $this->type = $user->identity->type ?? '';
            $this->institution_id = $user->identity->institution_id ?? '';
            $this->study_program_id = $user->identity->study_program_id ?? '';
        }
    }

    /**
     * Validation rules for updating the user.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->userId)],
            'selectedRole' => ['nullable', 'string', Rule::exists('roles', 'name')],
            'emailVerified' => ['boolean'],
            'identity_id' => ['required', 'string', 'max:255', Rule::unique('identities', 'identity_id')->ignore($this->user->identity?->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'sinta_id' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in('dosen', 'mahasiswa')],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
        ];
    }

    /**
     * Persist the updated user information.
     */
    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            $user = $this->user;

            $user->fill([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            if ($validated['emailVerified']) {
                $user->email_verified_at = $user->email_verified_at ?? now();
            } else {
                $user->email_verified_at = null;
            }

            $user->save();

            // Update or create identity
            $user->identity()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'identity_id' => $validated['identity_id'],
                    'address' => $validated['address'],
                    'birthdate' => $validated['birthdate'],
                    'birthplace' => $validated['birthplace'],
                    'sinta_id' => $validated['sinta_id'],
                    'type' => $validated['type'],
                    // 'institution_id' => $validated['institution_id'],
                    'study_program_id' => $validated['study_program_id'],
                ]
            );

            if ($validated['selectedRole']) {
                $user->syncRoles([$validated['selectedRole']]);
            } else {
                $user->syncRoles([]);
            }
        });

        $this->selectedRole = $this->user->roles()->first()?->name;
        $this->emailVerified = $this->user->hasVerifiedEmail();

        session()->flash('success', 'Pengguna telah diperbarui.');

        $this->dispatch('user-updated');
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        $user = $this->user;

        return view('livewire.users.edit', [
            'user' => $user,
            'roleOptions' => $this->roleOptions(),
            'institutionOptions' => $this->institutionOptions(),
            'studyProgramOptions' => $this->studyProgramOptions(),
        ]);
    }

    /**
     * Resolve the current user model.
     */
    public function getUserProperty(): User
    {
        return User::query()
            ->with(['roles', 'identity'])
            ->findOrFail($this->userId);
    }

    /**
     * Retrieve role options for the selection control.
     *
     * @return array<int, array<string, string>>
     */
    protected function roleOptions(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get()
            ->map(fn(Role $role) => [
                'value' => $role->name,
                'label' => str($role->name)->title()->toString(),
            ])
            ->values()
            ->all();
    }

    /**
     * Retrieve institution options for the selection control.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function institutionOptions(): array
    {
        return Institution::query()
            ->orderBy('name')
            ->get()
            ->map(fn(Institution $institution) => [
                'value' => $institution->id,
                'label' => $institution->name,
            ])
            ->values()
            ->all();
    }

    /**
     * Retrieve study program options for the current institution.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function studyProgramOptions(): array
    {
        if (! $this->institution_id) {
            return [];
        }

        return \App\Models\StudyProgram::query()
            ->where('institution_id', $this->institution_id)
            ->orderBy('name')
            ->get()
            ->map(fn(\App\Models\StudyProgram $program) => [
                'value' => $program->id,
                'label' => $program->name,
            ])
            ->values()
            ->all();
    }
}
