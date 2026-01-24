<?php

namespace App\Livewire\Settings;

use App\Livewire\Concerns\HasToast;
use App\Models\Faculty;
use App\Models\Institution;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfileForm extends Component
{
    use HasToast, WithFileUploads;

    public string $name = '';

    public string $email = '';

    public $photo;

    // Identity fields
    public string $identity_id = '';

    public string $type = '';

    public string $sinta_id = '';

    public string $address = '';

    public string $birthdate = '';

    public string $birthplace = '';

    public ?int $institution_id = null;

    public ?int $faculty_id = null;

    public ?int $study_program_id = null;

    public array $institutions = [];

    public array $faculties = [];

    public array $studyPrograms = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();

        // Load basic user data
        $this->name = $user->name;
        $this->email = $user->email;

        // Load identity data
        if ($user->identity) {
            $this->identity_id = $user->identity->identity_id;
            $this->type = $user->identity->type;
            $this->sinta_id = $user->identity->sinta_id ?? '';
            $this->address = $user->identity->address ?? '';
            $this->birthdate = $user->identity->birthdate?->format('Y-m-d') ?? '';
            $this->birthplace = $user->identity->birthplace ?? '';
            $this->institution_id = $user->identity->institution_id;
            $this->faculty_id = $user->identity->faculty_id;
            $this->study_program_id = $user->identity->study_program_id;
        }

        // Load institutions for dropdown
        $this->institutions = Institution::orderBy('name')->get()->toArray();

        // Load faculties based on selected institution
        if ($this->institution_id) {
            $this->faculties = Faculty::where('institution_id', $this->institution_id)
                ->orderBy('name')
                ->get()
                ->toArray();
        }

        // Load study programs based on selected faculty
        if ($this->faculty_id) {
            $this->studyPrograms = StudyProgram::where('faculty_id', $this->faculty_id)
                ->orderBy('name')
                ->get()
                ->toArray();
        } elseif ($this->institution_id) {
            $this->studyPrograms = StudyProgram::where('institution_id', $this->institution_id)
                ->orderBy('name')
                ->get()
                ->toArray();
        }
    }

    /**
     * Updated institution.
     */
    public function updatedInstitutionId(): void
    {
        $this->faculty_id = null;
        $this->study_program_id = null;
        $this->faculties = Faculty::where('institution_id', $this->institution_id)
            ->orderBy('name')
            ->get()
            ->toArray();
        $this->studyPrograms = [];
    }

    /**
     * Updated faculty.
     */
    public function updatedFacultyId(): void
    {
        $this->study_program_id = null;
        $this->studyPrograms = StudyProgram::where('faculty_id', $this->faculty_id)
            ->orderBy('name')
            ->get()
            ->toArray();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'identity_id' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:dosen,mahasiswa'],
            'sinta_id' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'birthdate' => ['nullable', 'date'],
            'birthplace' => ['nullable', 'string', 'max:255'],
            'institution_id' => ['nullable', 'exists:institutions,id'],
            'faculty_id' => ['nullable', 'exists:faculties,id'],
            'study_program_id' => ['nullable', 'exists:study_programs,id'],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);

        // Update user data
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($this->photo) {
            $user->addMedia($this->photo->getRealPath())
                ->usingFileName($this->photo->getClientOriginalName())
                ->toMediaCollection('avatar');

            $this->reset('photo');
        }

        // Update or create identity
        $identityData = [
            'identity_id' => $validated['identity_id'],
            'type' => $validated['type'],
            'sinta_id' => $validated['sinta_id'] ?: null,
            'address' => $validated['address'] ?: null,
            'birthdate' => $validated['birthdate'] ?: null,
            'birthplace' => $validated['birthplace'] ?: null,
            'institution_id' => $validated['institution_id'] ?? null,
            'faculty_id' => $validated['faculty_id'] ?? null,
            'study_program_id' => $validated['study_program_id'] ?? null,
        ];

        $user->identity()->updateOrCreate(
            ['user_id' => $user->id],
            $identityData
        );

        $this->dispatch('profile-updated', name: $user->name);

        $message = 'Profile berhasil diperbarui.';
        session()->flash('success', $message);
        $this->toastSuccess($message);
    }

    /**
     * Remove the current user's profile picture.
     */
    public function removeAvatar(): void
    {
        $user = Auth::user();

        $user->clearMediaCollection('avatar');

        if ($user->identity && $user->identity->profile_picture) {
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($user->identity->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->identity->profile_picture);
            }

            $user->identity->update(['profile_picture' => null]);
        }

        $this->dispatch('profile-updated', name: $user->name);
        $this->toastSuccess('Foto profil berhasil dihapus.');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    /**
     * Reset the form to original values.
     */
    public function resetForm(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;

        if ($user->identity) {
            $this->identity_id = $user->identity->identity_id;
            $this->type = $user->identity->type;
            $this->sinta_id = $user->identity->sinta_id ?? '';
            $this->address = $user->identity->address ?? '';
            $this->birthdate = $user->identity->birthdate?->format('Y-m-d') ?? '';
            $this->birthplace = $user->identity->birthplace ?? '';
            $this->institution_id = $user->identity->institution_id;
            $this->faculty_id = $user->identity->faculty_id;
            $this->study_program_id = $user->identity->study_program_id;

            // Reload faculties based on institution
            if ($this->institution_id) {
                $this->faculties = Faculty::where('institution_id', $this->institution_id)
                    ->orderBy('name')
                    ->get()
                    ->toArray();
            }

            // Reload study programs based on faculty
            if ($this->faculty_id) {
                $this->studyPrograms = StudyProgram::where('faculty_id', $this->faculty_id)
                    ->orderBy('name')
                    ->get()
                    ->toArray();
            } elseif ($this->institution_id) {
                $this->studyPrograms = StudyProgram::where('institution_id', $this->institution_id)
                    ->orderBy('name')
                    ->get()
                    ->toArray();
            }
        }

        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.settings.profile-form');
    }
}
