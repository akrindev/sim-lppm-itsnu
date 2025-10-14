<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app', ['title' => 'Users', 'pageTitle' => 'Kelola Pengguna', 'pageSubtitle' => 'Kelola data pengguna'])]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'all')]
    public string $role = 'all';

    #[Url(except: 'all')]
    public string $status = 'all';

    public int $perPage = 10;

    #[On('user-created')]
    public function handleUserCreated(): void
    {
        $this->resetPage();
    }

    /**
     * Reset the paginator when the search term is updated.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset the paginator when the role filter is updated.
     */
    public function updatingRole(): void
    {
        $this->resetPage();
    }

    /**
     * Reset the paginator when the status filter is updated.
     */
    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.users.index', [
            'users' => $this->users(),
            'roleOptions' => $this->roleOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    /**
     * Retrieve paginated users with the current filters applied.
     */
    protected function users(): LengthAwarePaginator
    {
        $perPage = max(5, min(50, $this->perPage));
        $search = trim($this->search);

        return User::query()
            ->with(['roles', 'identity'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('identity', fn ($relation) => $relation->where('identity_id', 'like', "%{$search}%"));
                });
            })
            ->when($this->role !== 'all', fn ($query) => $query->whereHas('roles', fn ($relation) => $relation->where('name', $this->role)))
            ->when($this->status !== 'all', function ($query) {
                if ($this->status === 'verified') {
                    $query->whereNotNull('email_verified_at');
                }

                if ($this->status === 'unverified') {
                    $query->whereNull('email_verified_at');
                }
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Build the available role filter options.
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
            ->prepend([
                'value' => 'all',
                'label' => __('All roles'),
            ])
            ->values()
            ->all();
    }

    /**
     * Build the available status filter options.
     *
     * @return array<int, array<string, string>>
     */
    protected function statusOptions(): array
    {
        return [
            [
                'value' => 'all',
                'label' => __('All status'),
            ],
            [
                'value' => 'verified',
                'label' => __('Verified'),
            ],
            [
                'value' => 'unverified',
                'label' => __('Pending verification'),
            ],
        ];
    }
}
