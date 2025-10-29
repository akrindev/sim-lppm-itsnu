<?php

namespace App\Livewire\Users;

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

    public ?string $selectedRole = null;

    // Identity fields
    public string $identity_id = '';

    public string $address = '';

    public ?string $birthdate = null;

    public string $birthplace = '';

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
            ]);

            if ($validated['selectedRole']) {
                $user->syncRoles([$validated['selectedRole']]);
            }
        });

        session()->flash('users.status', __('New user created successfully.'));

        $this->redirect(route('users.index'), navigate: true);
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.users.create', [
            'roleOptions' => $this->roleOptions(),
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
            'selectedRole' => ['nullable', 'string', Rule::exists('roles', 'name')],
            'identity_id' => ['required', 'string', 'max:255', 'unique:identities,identity_id'],
            'address' => ['nullable', 'string', 'max:500'],
            'birthdate' => ['nullable', 'date', 'before:today'],
            'birthplace' => ['nullable', 'string', 'max:255'],
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
}
