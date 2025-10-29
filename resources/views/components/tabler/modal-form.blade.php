@props([
    'id',
    'title' => null,
    'action' => null,
    'method' => 'POST',
    'submitText' => 'Save',
    'cancelText' => 'Cancel',
    'wireIgnore' => true,
    'componentId' => null,
    'onSubmit' => null,
    'onCancel' => null,
    'size' => 'lg', // sm, md, lg, xl
    'scrollable' => true,
    'showCloseButton' => true,
])

@php
    $formId = $id . '-form';
    $methodField = '';
    $csrfField = '';

    if (in_array(strtoupper($method), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        $methodField = method_field($method);
    }

    if (strtoupper($method) === 'POST') {
        $csrfField = csrf_field();
    }
@endphp

<x-tabler.modal :id="$id" :title="$title" :wire-ignore="$wireIgnore" :component-id="$componentId" :size="$size"
    :scrollable="$scrollable" :close-button="$showCloseButton" centered="true" class="modal-form">
    <x-slot name="body">
        @if ($action)
            <form id="{{ $formId }}" wire:submit.prevent="handleSubmit" x-data="modalForm()">
                {{ $csrfField }}
                {{ $methodField }}

                <div wire:ignore>
                    {{ $slot }}
                </div>
            </form>
        @else
            <div wire:ignore>
                {{ $slot }}
            </div>
        @endif
    </x-slot>

    @if ($submitText || $cancelText)
        <x-slot name="footer">
            @if ($cancelText)
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    {{ $cancelText }}
                </button>
            @endif

            @if ($submitText)
                <button type="button" class="btn btn-primary"
                    @if ($action) x-on:click="submitForm"
                    @elseif($onSubmit && $componentId)
                        x-on:click="
                            const component = window.Livewire?.find('{{ $componentId }}');
                            component?.call('{{ $onSubmit }}');
                        " @endif>
                    <span x-show="!loading" x-text="submitText || '{{ $submitText }}'"></span>
                    <span x-show="loading" class="me-1 spinner-border spinner-border-sm" role="status"></span>
                    <span x-show="loading">Saving...</span>
                </button>
            @endif
        </x-slot>
    @endif
</x-tabler.modal>

@once
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                // Alpine component for form modal functionality
                window.modalForm = () => ({
                    loading: false,
                    submitText: '{{ $submitText }}',

                    init() {
                        this.$watch('loading', (value) => {
                            const modal = document.getElementById('{{ $id }}');
                            if (modal && Bootstrap.Modal) {
                                // Handle loading state for modal backdrop
                                if (value) {
                                    modal.classList.add('modal-loading');
                                } else {
                                    modal.classList.remove('modal-loading');
                                }
                            }
                        });
                    },

                    submitForm() {
                        const form = document.getElementById('{{ $formId }}');
                        if (form) {
                            this.loading = true;

                            // Trigger Livewire form submission
                            @this.call('handleSubmit').finally(() => {
                                this.loading = false;
                            });
                        }
                    },

                    resetForm() {
                        const form = document.getElementById('{{ $formId }}');
                        if (form) {
                            form.reset();
                        }
                        this.loading = false;
                    }
                });

                // Auto-submit on Enter key
                document.querySelectorAll('.modal-form form').forEach(form => {
                    form.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            const submitBtn = form.closest('.modal').querySelector('.btn-primary');
                            submitBtn?.click();
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce

{{--
Usage Example:
<x-tabler.modal-form
    id="create-user-modal"
    title="Create New User"
    action="/users"
    method="POST"
    submit-text="Create User"
    component-id="{{ $this->id }}"
    on-submit="createUser"
>
    <div class="row">
        <div class="col-12">
            <flux:input
                label="Name"
                wire:model="form.name"
                placeholder="Enter user name"
                required="true"
            />
        </div>
        <div class="mt-3 col-12">
            <flux:input
                label="Email"
                type="email"
                wire:model="form.email"
                placeholder="Enter email address"
                required="true"
            />
        </div>
        <div class="mt-3 col-12">
            <flux:select
                label="Role"
                wire:model="form.role"
                required="true"
            >
                <option value="">Select Role</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </flux:select>
        </div>
    </div>
</x-tabler.modal-form>
--}}
