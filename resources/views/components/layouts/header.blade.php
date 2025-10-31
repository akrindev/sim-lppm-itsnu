<!-- BEGIN NAVBAR  -->
<div>
    <header class="navbar navbar-expand-md d-print-none">
        <div class="container-xl">
            <!-- BEGIN NAVBAR TOGGLER -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
                aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- END NAVBAR TOGGLER -->
            <!-- BEGIN NAVBAR LOGO -->
            <div class="pe-md-3 navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0">
                <a href="/" aria-label="{{ config('app.name') }}">
                    <img src="/logo.png" alt="LPPM ITSNU Pekalongan" width="45" height="45">
                    <span>{{ config('app.name') }}</span>
                </a>
            </div>
            <!-- END NAVBAR LOGO -->
            <div class="order-md-last navbar-nav flex-row">
                <div class="d-md-flex d-none">
                    <div class="nav-item">
                        <a href="?theme=dark" class="nav-link hide-theme-dark px-0" title="Enable dark mode"
                            data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/moon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-1">
                                <path
                                    d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                            </svg>
                        </a>
                        <a href="?theme=light" class="nav-link hide-theme-light px-0" title="Enable light mode"
                            data-bs-toggle="tooltip" data-bs-placement="bottom">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/sun -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-1">
                                <path d="M12 12m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                <path
                                    d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                            </svg>
                        </a>
                    </div>
                    <div class="d-md-flex nav-item dropdown d-none">
                        <a href="#" class="nav-link px-0" data-bs-toggle="dropdown" tabindex="-1"
                            aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-1">
                                <path
                                    d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                            </svg>
                            <span class="bg-red badge"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card">
                            <div class="card">
                                <div class="d-flex card-header">
                                    <h3 class="card-title">Notifications</h3>
                                    <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                                </div>
                                <div class="list-group list-group-flush list-group-hoverable">
                                    <div class="list-group-item">
                                        <div class="align-items-center row">
                                            <div class="col-auto"><span
                                                    class="d-block bg-red status-dot status-dot-animated"></span>
                                            </div>
                                            <div class="text-truncate col">
                                                <a href="#" class="d-block text-body">Example 1</a>
                                                <div class="d-block mt-n1 text-secondary text-truncate">Change
                                                    deprecated html tags to text decoration classes (#29604)</div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="#" class="list-group-item-actions">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="text-muted icon icon-2">
                                                        <path
                                                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="align-items-center row">
                                            <div class="col-auto"><span class="d-block status-dot"></span></div>
                                            <div class="text-truncate col">
                                                <a href="#" class="d-block text-body">Example 2</a>
                                                <div class="d-block mt-n1 text-secondary text-truncate">
                                                    justify-content:between ⇒ justify-content:space-between (#29734)
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="#" class="list-group-item-actions show">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="text-yellow icon icon-2">
                                                        <path
                                                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="align-items-center row">
                                            <div class="col-auto"><span class="d-block status-dot"></span></div>
                                            <div class="text-truncate col">
                                                <a href="#" class="d-block text-body">Example 3</a>
                                                <div class="d-block mt-n1 text-secondary text-truncate">Update
                                                    change-version.js (#29736)</div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="#" class="list-group-item-actions">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="text-muted icon icon-2">
                                                        <path
                                                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="list-group-item">
                                        <div class="align-items-center row">
                                            <div class="col-auto"><span
                                                    class="d-block bg-green status-dot status-dot-animated"></span>
                                            </div>
                                            <div class="text-truncate col">
                                                <a href="#" class="d-block text-body">Example 4</a>
                                                <div class="d-block mt-n1 text-secondary text-truncate">Regenerate
                                                    package-lock.json (#29730)</div>
                                            </div>
                                            <div class="col-auto">
                                                <a href="#" class="list-group-item-actions">
                                                    <!-- Download SVG icon from http://tabler.io/icons/icon/star -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="text-muted icon icon-2">
                                                        <path
                                                            d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <a href="#" class="w-100 btn btn-2"> Archive all </a>
                                        </div>
                                        <div class="col">
                                            <a href="#" class="w-100 btn btn-2"> Mark all as read </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nav-item dropdown">
                    <a href="#" class="d-flex nav-link lh-1 p-0 px-2" data-bs-toggle="dropdown"
                        aria-label="Open user menu">
                        <span class="avatar avatar-sm" style="background-image: url(./static/avatars/000m.jpg)">
                        </span>
                        <div class="d-xl-block d-none ps-2">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="text-secondary small mt-1">{{ Auth::user()->email }}</div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a href="#" class="dropdown-item">Status</a>
                        <a href="{{ route('settings.profile') }}" class="dropdown-item">Profile</a>
                        <a href="#" class="dropdown-item">Feedback</a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('settings') }}" class="dropdown-item">Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-100 dropdown-item text-start">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <header class="navbar-expand-md">
        <div class="navbar-collapse collapse" id="navbar-menu">
            <div class="navbar">
                <div class="container-xl">
                    <div class="flex-column flex-fill flex-md-row align-items-center row">
                        <div class="col">
                            <!-- BEGIN NAVBAR MENU -->
                            @if (!empty($headerMenuItems))
                                <ul class="navbar-nav">
                                    @foreach ($headerMenuItems as $menuItem)
                                        @php
                                            $isDropdown = ($menuItem['type'] ?? 'link') === 'dropdown';
                                            $isActive = !empty($menuItem['active']);
                                        @endphp

                                        @if ($isDropdown)
                                            <li class="nav-item dropdown{{ $isActive ? ' active' : '' }}">
                                                <a class="nav-link dropdown-toggle" href="#"
                                                    data-bs-toggle="dropdown"
                                                    data-bs-auto-close="{{ $menuItem['dropdown']['auto_close'] ?? 'outside' }}"
                                                    role="button" aria-expanded="false">
                                                    <span class="d-lg-inline-block nav-link-icon d-md-none">
                                                        @if (!empty($menuItem['icon']))
                                                            @include(
                                                                'components.layouts.partials.menu.icon',
                                                                [
                                                                    'name' => $menuItem['icon'],
                                                                    'class' => 'icon icon-1',
                                                                ]
                                                            )
                                                        @endif
                                                    </span>
                                                    <span class="nav-link-title"> {{ $menuItem['title'] }} </span>
                                                </a>
                                                <div class="dropdown-menu">
                                                    @include(
                                                        'components.layouts.partials.menu.dropdown-content',
                                                        [
                                                            'dropdown' => $menuItem['dropdown'] ?? [],
                                                        ]
                                                    )
                                                </div>
                                            </li>
                                        @else
                                            <li class="nav-item{{ $isActive ? ' active' : '' }}">
                                                <a class="nav-link" href="{{ $menuItem['href'] ?? '#' }}"
                                                    wire:navigate>
                                                    <span class="d-lg-inline-block nav-link-icon d-md-none">
                                                        @if (!empty($menuItem['icon']))
                                                            @include(
                                                                'components.layouts.partials.menu.icon',
                                                                [
                                                                    'name' => $menuItem['icon'],
                                                                    'class' => 'icon icon-1',
                                                                ]
                                                            )
                                                        @endif
                                                    </span>
                                                    <span class="nav-link-title"> {{ $menuItem['title'] }} </span>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @endif
                            <!-- END NAVBAR MENU -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
</div>
<!-- END NAVBAR  -->
