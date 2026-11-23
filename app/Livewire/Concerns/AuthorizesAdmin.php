<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;

/**
 * Reusable admin-guard authorization guard for Livewire components.
 * Mirrors the existing convention (abort 403 when the permission is missing).
 */
trait AuthorizesAdmin
{
    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
}
