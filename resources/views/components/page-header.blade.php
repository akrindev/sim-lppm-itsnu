@props(['title', 'subtitle' => null])

<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="align-items-center row g-2">
            <div class="col-12 col-md">
                <h2 class="page-title">{{ $title }}</h2>
                @if($subtitle)
                    <div class="mt-1 text-secondary">{{ $subtitle }}</div>
                @endif
                {{ $slot }}
            </div>
            <!-- Page title actions -->
            @isset($actions)
                <div class="col-12 col-md-auto ms-auto d-print-none">
                    <div class="d-flex">
                        {{ $actions }}
                    </div>
                </div>
            @endisset
        </div>
    </div>
</div>
