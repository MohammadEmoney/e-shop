<?php

namespace App\Livewire\Concerns;

/**
 * Reusable flash + toast notification helper for Livewire components.
 * Keeps the session flash (for fallback) and dispatches a browser 'toast'
 * event that the admin layout renders via toastr.
 */
trait DispatchesToasts
{
    /**
     * Flash a status message and dispatch a toastr notification.
     */
    protected function flashToast(string $message, string $type = 'success'): void
    {
        session()->flash('status', $message);
        session()->flash('toast_type', $type);

        $this->dispatch('toast', message: $message, type: $type);
    }
}
