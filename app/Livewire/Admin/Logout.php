<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Logs the admin out of ONLY the admin guard. The customer (web) guard's
 * session remains untouched, so a logged-in customer is not logged out.
 */
class Logout extends Component
{
    public function logout(): void
    {
        Auth::guard('admin')->logout();

        // Regenerate the token/session but keep the web (customer) session data.
        $session = request()->session();
        $session->invalidate();
        $session->regenerateToken();

        $this->redirect(route('admin.login'), navigate: true);
    }

    public function render(): string
    {
        return 'livewire.admin.logout';
    }
}
