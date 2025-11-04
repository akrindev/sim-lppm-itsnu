<div>
    <a href="#" class="px-0 nav-link" data-bs-toggle="dropdown" tabindex="-1"
        aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
            <path
                d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
            <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
        </svg>
        @if ($unreadCount > 0)
            <span class="bg-red badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
        <div class="card">
            <div class="d-flex card-header">
                <h3 class="card-title">Notifikasi</h3>
                <div class="ms-auto btn-close" data-bs-dismiss="dropdown"></div>
            </div>
            @if ($unreadNotifications->count() > 0)
                <div class="list-group list-group-flush list-group-hoverable">
                    @foreach ($unreadNotifications as $notification)
                        @php
                            $data = json_decode($notification->data, true);
                            $icon = $this->getIconAttribute($notification->type);
                            $timeAgo = $notification->created_at->diffForHumans();
                        @endphp

                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="avatar avatar-sm" style="background-color: #3b82f620; color: #3b82f6;">
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
                                    <a href="{{ $data['link'] ?? '#' }}" class="d-block text-body">
                                        <strong>{{ $data['title'] ?? 'Notifikasi Baru' }}</strong>
                                    </a>
                                    <div class="d-block mt-n1 text-secondary text-truncate">
                                        {{ $data['message'] ?? '' }}
                                    </div>
                                    <div class="mt-1">
                                        <span class="text-secondary" style="font-size: 0.75rem;">{{ $timeAgo }}</span>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <button wire:click="markAsRead('{{ $notification->id }}')" class="btn-action">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                            <path d="M18 6L6 18M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('notifications') }}" class="w-100 btn btn-2"> Lihat Semua </a>
                        </div>
                        @if ($unreadCount > 0)
                            <div class="col">
                                <button wire:click="markAllAsRead" class="w-100 btn btn-2"> Tandai Dibaca </button>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card-body">
                    <div class="empty">
                        <div class="empty-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-muted">
                                <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6"/>
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1"/>
                            </svg>
                        </div>
                        <p class="empty-title">Tidak ada notifikasi</p>
                        <p class="empty-subtitle text-muted">Anda belum memiliki notifikasi baru</p>
                        <div class="empty-action">
                            <a href="{{ route('notifications') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                    <circle cx="11" cy="11" r="8"/>
                                    <path d="m21 21-4.35-4.35"/>
                                </svg>
                                Lihat Semua Notifikasi
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

