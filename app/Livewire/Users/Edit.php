<?php

namespace App\Livewire\Users;

use App\Livewire\Concerns\HasToast;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app', ['title' => 'Edit User', 'pageTitle' => 'Edit User', 'pageSubtitle' => 'Update the user profile and role assignments'])]
class Edit extends Component
{
    use HasToast;

    public string $userId;

    public string $name = '';

    public string $email = '';

    public array $selectedRoles = [];

    public bool $emailVerified = false;

    // Identity fields
    public string $identity_id = '';

    public string $address = '';

    public ?string $birthdate = null;

    public string $birthplace = '';

    public ?string $sinta_id = null;

    public ?string $type = null;

    public ?string $institution_id = null;

    public ?string $institution_name = null;

    public ?string $faculty_id = null;

    public ?string $study_program_id = null;

    /**
     * Hydrate the component state from the selected user.
     */
    public function mount(User $user): void
    {
        $this->userId = $user->getKey();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->getRoleNames()->toArray();
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
            $this->institution_name = $user->identity->institution->name ?? '';
            $this->faculty_id = $user->identity->faculty_id ?? '';
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
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['string', Rule::exists('roles', 'name')],
            'emailVerified' => ['boolean'],
            'identity_id' => ['required', 'string', 'max:255', Rule::unique('identities', 'identity_id')->ignore($this->user->identity?->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'sinta_id' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in('dosen', 'mahasiswa')],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'faculty_id' => [
                'required',
                'exists:faculties,id',
                Rule::exists('faculties', 'id')->where('institution_id', $this->institution_id),
            ],
            'study_program_id' => [
                'required',
                'exists:study_programs,id',
                Rule::exists('study_programs', 'id')->where('faculty_id', $this->faculty_id),
            ],
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
                    'institution_id' => $validated['institution_id'],
                    'faculty_id' => $validated['faculty_id'] ?? null,
                    'study_program_id' => $validated['study_program_id'],
                ]
            );

            // Sync multiple roles
            $user->syncRoles($validated['selectedRoles']);
        });

        $this->selectedRoles = $this->user->getRoleNames()->toArray();
        $this->emailVerified = $this->user->hasVerifiedEmail();

        $message = 'Pengguna telah diperbarui.';
        session()->flash('success', $message);
        $this->toastSuccess($message);

        $this->dispatch('user-updated');
    }

    /**
     * Render the component view.
     * All public properties and computed properties are automatically available in the view.
     */
    public function render(): View
    {
        return view('livewire.users.edit');
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
     * Cached computed property that returns available roles.
     *
     * @return array<int, array<string, string>>
     */
    #[Computed]
    public function roleOptions(): array
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
     * Cached computed property that returns all available institutions.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function institutionOptions(): array
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
     * Retrieve faculty options for the current institution.
     * Reactive computed property that automatically updates when institution_id changes.
     * Returns empty array when no institution is selected.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function facultyOptions(): array
    {
        // Return empty array if no institution is selected
        if (! $this->institution_id) {
            return [];
        }

        // Fetch faculties belonging to the selected institution
        return Faculty::query()
            ->where('institution_id', $this->institution_id)
            ->orderBy('name')
            ->get()
            ->map(fn(Faculty $faculty) => [
                'value' => $faculty->id,
                'label' => $faculty->name,
            ])
            ->values()
            ->all();
    }

    /**
     * Retrieve study program options for the current faculty.
     * Reactive computed property that automatically updates when faculty_id changes.
     * Returns empty array when no faculty is selected.
     *
     * @return array<int, array<string, mixed>>
     */
    #[Computed]
    public function studyProgramOptions(): array
    {
        // Return empty array if no faculty is selected
        if (! $this->faculty_id) {
            return [];
        }

        // Fetch study programs belonging to the selected faculty
        return \App\Models\StudyProgram::query()
            ->where('faculty_id', $this->faculty_id)
            ->orderBy('name')
            ->get()
            ->map(fn(\App\Models\StudyProgram $program) => [
                'value' => $program->id,
                'label' => $program->name,
            ])
            ->values()
            ->all();
    }

    /**
     * Updated institution.
     */
    public function updatedInstitutionId(): void
    {
        $this->faculty_id = null;
        $this->study_program_id = null;
    }

    /**
     * Updated faculty.
     */
    public function updatedFacultyId(): void
    {
        $this->study_program_id = null;
    }
}
