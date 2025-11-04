<?php

namespace App\Livewire\Users;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app', ['title' => 'Create User', 'pageTitle' => 'Create User', 'pageSubtitle' => 'Add a new user to the system'])]
class Create extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $selectedRoles = [];

    // Identity fields
    public string $identity_id = '';

    public string $address = '';

    public ?string $birthdate = null;

    public string $birthplace = '';

    public ?string $sinta_id = null;

    public ?string $type = null;

    public ?string $institution_id = '1';

    public ?string $study_program_id = null;

    /**
     * Persist the newly created user.
     */
    public function save(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
            ]);

            // Create identity
            $user->identity()->create([
                'identity_id' => $validated['identity_id'],
                'address' => $validated['address'],
                'birthdate' => $validated['birthdate'],
                'birthplace' => $validated['birthplace'],
                'sinta_id' => $validated['sinta_id'],
                'type' => $validated['type'],
                'study_program_id' => $validated['study_program_id'],
            ]);

            // Sync multiple roles
            if (! empty($validated['selectedRoles'])) {
                $user->syncRoles($validated['selectedRoles']);
            }
        });

        session()->flash('success', 'Pengguna baru telah dibuat.');

        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.users.create', [
            'roleOptions' => $this->roleOptions(),
            'institutionOptions' => $this->institutionOptions(),
            'studyProgramOptions' => $this->studyProgramOptions(),
        ]);
    }

    /**
     * Validation rules for the create form.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
            'selectedRoles' => ['nullable', 'array'],
            'selectedRoles.*' => ['string', Rule::exists('roles', 'name')],
            'identity_id' => ['required', 'string', 'max:255', 'unique:identities,identity_id'],
            'address' => ['nullable', 'string', 'max:500'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'sinta_id' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in('dosen', 'mahasiswa')],
            'institution_id' => ['required', 'exists:institutions,id'],
            'study_program_id' => ['required', 'exists:study_programs,id'],
        ];
    }

    /**
     * Retrieve role options shown in the create modal.
     *
     * @return array<int, array<string, string>>
     */
    protected function roleOptions(): array
    {
        return Role::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Role $role) => [
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
            ->map(fn (Institution $institution) => [
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
            ->map(fn (\App\Models\StudyProgram $program) => [
                'value' => $program->id,
                'label' => $program->name,
            ])
            ->values()
            ->all();
    }
}
