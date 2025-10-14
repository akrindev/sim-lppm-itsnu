<div>
    <x-slot:pageActions>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M12 5v14"></path>
                <path d="M5 12h14"></path>
            </svg>
            {{ __('Tambah Pengguna') }}
        </a>
    </x-slot:pageActions>

    @if (session('users.status'))
        <div class="mb-3 alert alert-success alert-important" role="alert">
            {{ session('users.status') }}
        </div>
    @endif

    <div class="card card-stacked">
        <div class="border-bottom card-body">
            <div class="align-items-end row g-3">
                <div class="col-md-5">
                    <label for="user-search" class="form-label">{{ __('Cari') }}</label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M10 17a7 7 0 1 0 0 -14 7 7 0 0 0 0 14z"></path>
                                <path d="M21 21l-6 -6"></path>
                            </svg>
                        </span>
                        <input id="user-search" type="search" class="form-control"
                            wire:model.live.live.debounce.400ms="search"
                            placeholder="{{ __('Cari nama, email, atau ID...') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <label for="role-filter" class="form-label">{{ __('Peran') }}</label>
                    <select id="role-filter" class="form-select" wire:model.live="role">
                        @foreach ($roleOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="status-filter" class="form-label">{{ __('Status') }}</label>
                    <select id="status-filter" class="form-select" wire:model.live="status">
                        @foreach ($statusOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="card-table table table-vcenter">
                <thead>
                    <tr>
                        <th>{{ __('Nama') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Peran') }}</th>
                        <th class="text-center">{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Bergabung') }}</th>
                        <th class="text-end"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td class="fw-semibold">
                                <div>{{ $user->name }}</div>
                                <div class="text-secondary text-truncate">{{ $user->identity?->identity_id }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->roles->isNotEmpty())
                                    <span class="bg-primary-lt text-primary badge">{{ str($user->roles->first()->name)->title() }}</span>
                                @else
                                    <span class="text-secondary">{{ __('Tidak ada peran') }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($user->hasVerifiedEmail())
                                    <span class="bg-green-lt text-green badge">{{ __('Terverifikasi') }}</span>
                                @else
                                    <span class="bg-yellow-lt text-yellow badge">{{ __('Menunggu') }}</span>
                                @endif
                            </td>
                            <td class="text-secondary text-end">
                                {{ optional($user->created_at)->translatedFormat('d M Y') }}
                            </td>
                            <td class="text-end">
                                <div class="justify-content-end btn-list">
                                    <a href="{{ route('users.show', $user) }}"
                                        class="btn-outline-secondary btn btn-sm">
                                        {{ __('Lihat') }}
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                        {{ __('Ubah') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-5 text-secondary text-center">
                                {{ __('Tidak ada pengguna yang cocok dengan filter Anda.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex align-items-center justify-content-between card-footer">
            <div class="text-secondary">
                {{ trans_choice('{0}Tidak ada data|{1}Menampilkan :count pengguna|[2,*]Menampilkan :count pengguna', $users->count(), ['count' => $users->count()]) }}
            </div>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
