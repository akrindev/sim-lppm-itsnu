<?php

namespace App\Livewire\Concerns;

/**
 * Provides toast notification methods for Livewire components.
 *
 * Usage:
 *   use HasToast;
 *
 *   $this->toast('success', 'Data berhasil disimpan');
 *   $this->toastSuccess('Data berhasil disimpan');
 *   $this->toastError('Gagal menyimpan data');
 */
trait HasToast
{
    /**
     * Show a toast notification.
     *
     * @param  string  $variant  One of: success, danger, warning, info
     * @param  string  $message  The toast message
     * @param  string|null  $title  Optional title (defaults based on variant)
     * @param  string  $position  Position: top-start, top-center, top-end, bottom-start, bottom-center, bottom-end
     * @param  int  $delay  Auto-hide delay in milliseconds
     */
    public function toast(
        string $variant,
        string $message,
        ?string $title = null,
        string $position = 'top-end',
        int $delay = 5000
    ): void {
        $this->dispatch('toast', [
            'message' => $message,
            'title' => $title ?? $this->getDefaultToastTitle($variant),
            'variant' => $variant,
            'position' => $position,
            'autoHide' => true,
            'delay' => $delay,
        ]);
    }

    /**
     * Show a success toast notification.
     */
    public function toastSuccess(string $message, ?string $title = null): void
    {
        $this->toast('success', $message, $title);
    }

    /**
     * Show an error toast notification.
     */
    public function toastError(string $message, ?string $title = null): void
    {
        $this->toast('danger', $message, $title);
    }

    /**
     * Show a warning toast notification.
     */
    public function toastWarning(string $message, ?string $title = null): void
    {
        $this->toast('warning', $message, $title);
    }

    /**
     * Show an info toast notification.
     */
    public function toastInfo(string $message, ?string $title = null): void
    {
        $this->toast('info', $message, $title);
    }

    /**
     * Get the default title based on variant.
     */
    protected function getDefaultToastTitle(string $variant): string
    {
        return match ($variant) {
            'success' => 'Berhasil',
            'danger' => 'Error',
            'warning' => 'Peringatan',
            'info' => 'Informasi',
            default => 'Notifikasi',
        };
    }
}
