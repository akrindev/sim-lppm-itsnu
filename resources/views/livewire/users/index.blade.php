<x-layouts.app
    :title="__('Users')"
    :pageTitle="__('Users Management')"
    :pageSubtitle="__('Manage system users and their permissions')"
>
    <x-slot:pageActions>
        <a href="#" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M12 5l0 14"></path>
                <path d="M5 12l14 0"></path>
            </svg>
            {{ __('Add User') }}
        </a>
    </x-slot:pageActions>

    <div class="card">
        <div class="card-body">
            <p>{{ __('User management content will be displayed here.') }}</p>
        </div>
    </div>
</x-layouts.app>
