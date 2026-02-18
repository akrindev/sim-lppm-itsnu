@props(['proposal'])

@php
    $workflowStatuses = \App\Enums\ProposalStatus::cases();
    $currentStatus = $proposal->status->value;

    $reachedStatuses = collect($proposal->statusLogs)
        ->pluck('status_after')
        ->filter()
        ->map(fn($status) => $status->value)
        ->values()
        ->all();

    $reachedStatuses[] = \App\Enums\ProposalStatus::DRAFT->value;
    $reachedStatuses[] = $currentStatus;
    $reachedStatuses = array_values(array_unique($reachedStatuses));
@endphp

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Progress Workflow Proposal</h3>
            <div class="text-secondary small">Checklist lengkap tahapan status proposal dari Draft sampai keputusan
                akhir.</div>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex flex-column justify-content-between mb-3 gap-2">
            <div class="text-secondary small">Status saat ini: {{ $proposal->status->description() }}</div>
            <x-tabler.badge :color="$proposal->status->color()">{{ $proposal->status->label() }}</x-tabler.badge>
        </div>

        <ul class="steps steps-vertical mb-0">
            @foreach ($workflowStatuses as $status)
                @php
                    $isCurrent = $currentStatus === $status->value;
                    $isReached = in_array($status->value, $reachedStatuses, true);
                    $stateLabel = $isCurrent ? 'Saat Ini' : ($isReached ? 'Selesai' : 'Belum');
                    $stateColor = $isCurrent ? 'primary' : ($isReached ? 'success' : 'secondary');
                    $itemClass = $isCurrent ? 'active' : ($isReached ? 'completed' : '');
                @endphp

                <li class="step-item {{ $itemClass }}">
                    <div class="d-flex align-items-start justify-content-between mb-1 gap-2">
                        <div class="fw-semibold">
                            {{ $status->label() }}
                        </div>
                        {{-- <x-tabler.badge :color="$stateColor">{{ $stateLabel }}</x-tabler.badge> --}}
                    </div>
                    <div class="text-secondary small">{{ $status->description() }}</div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
