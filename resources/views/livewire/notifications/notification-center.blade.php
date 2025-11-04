<div>
    <x-slot:title>Notifikasi</x-slot:title>
    <x-slot:pageTitle>Daftar Notifikasi</x-slot:pageTitle>
    <x-slot:pageSubtitle>Kelola semua notifikasi Anda di sini.</x-slot:pageSubtitle>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Notifikasi</h3>
                <div class="d-flex gap-2">
                    @if ($unreadCount > 0)
                        <button wire:click="markAllAsRead" class="btn btn-outline-primary btn-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0a9 9 0 0 1 18 0z"/>
                            </svg>
                            Tandai Semua Dibaca
                        </button>
                    @endif
                    <button wire:click="deleteAll" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus semua notifikasi?')">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <path d="M3 6h18"/>
                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                            <path d="M8 6V4c0-1 1-2 2-2h4c0-1 1-2 2-2v2"/>
                        </svg>
                        Hapus Semua
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Filter Tabs -->
            <div class="mb-3">
                <div class="nav nav-pills card-pills nav-fill">
                    <button wire:click="setFilter('all')" class="nav-link {{ $filter === 'all' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.35-4.35"/>
                        </svg>
                        Semua
                    </button>
                    <button wire:click="setFilter('unread')" class="nav-link {{ $filter === 'unread' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                        </svg>
                        Belum Dibaca
                        @if ($unreadCount > 0)
                            <span class="badge bg-primary ms-1">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <button wire:click="setFilter('read')" class="nav-link {{ $filter === 'read' ? 'active' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        Sudah Dibaca
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            @if ($notifications->count() > 0)
                <div class="list-group list-group-flush list-group-hoverable">
                    @foreach ($notifications as $notification)
                        @php
                            $data = json_decode($notification->data, true);
                            $isUnread = is_null($notification->read_at);
                            $icon = $this->getIconAttribute($notification->type);
                            $typeLabel = $this->getTypeLabelAttribute($notification->type);
                            $timeAgo = $notification->created_at->diffForHumans();
                        @endphp

                        <div class="list-group-item {{ $isUnread ? 'bg-primary-fg' : '' }}">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-sm" style="background-color: {{ $isUnread ? '#3b82f6' : '#6b7280' }}20; color: {{ $isUnread ? '#3b82f6' : '#6b7280' }};">
                                        @switch($icon)
                                            @case('file-text')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                                                    <polyline points="14 2 14 8 20 8"/>
                                                </svg>
                                                @break
                                            @case('check-circle')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                @break
                                            @case('user-check')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                    <circle cx="8.5" cy="7" r="4"/>
                                                    <path d="M20 8v6M23 11l-3 3-3-3"/>
                                                </svg>
                                                @break
                                            @case('check-square')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="9 11 12 14 22 4"/>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                                </svg>
                                                @break
                                            @case('award')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="12" cy="8" r="7"/>
                                                    <path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.11"/>
                                                </svg>
                                                @break
                                            @default
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                                                    <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                                                </svg>
                                        @endswitch
                                    </span>
                                </div>

                                <div class="col text-truncate">
                                    <a href="{{ $data['link'] ?? '#' }}" class="text-body-emphasis text-decoration-none d-block fw-semibold {{ $isUnread ? 'fw-bold' : '' }}">
                                        {{ $data['title'] ?? $typeLabel }}
                                    </a>
                                    <div class="d-block text-secondary text-truncate mt-n1 {{ $isUnread ? 'fw-semibold' : '' }}">
                                        {{ $data['message'] ?? '' }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="badge bg-secondary">{{ $typeLabel }}</span>
                                        <span class="text-secondary ms-2">{{ $timeAgo }}</span>
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <div class="dropdown">
                                        <a href="#" class="btn-action" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                                <circle cx="12" cy="12" r="1"/>
                                                <circle cx="12" cy="5" r="1"/>
                                                <circle cx="12" cy="19" r="1"/>
                                            </svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            @if ($isUnread)
                                                <button wire:click="markAsRead('{{ $notification->id }}')" class="dropdown-item">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                        <path d="M20 6L9 17l-5-5"/>
                                                    </svg>
                                                    Tandai Dibaca
                                                </button>
                                            @endif
                                            <button wire:click="deleteNotification('{{ $notification->id }}')" class="dropdown-item text-danger" onclick="return confirm('Yakin ingin menghapus notifikasi ini?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted mb-3">
                        <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                        <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                    </svg>
                    <h3 class="text-muted">Tidak ada notifikasi</h3>
                    <p class="text-secondary">Belum ada notifikasi{{ $filter !== 'all' ? ' dengan filter ini' : '' }}.</p>
                </div>
            @endif
        </div>
    </div>
</div>

