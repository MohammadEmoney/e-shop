<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin-guest')]
#[Title('ورود مدیران')]
class Login extends Component
{
    public string $username = '';

    public string $password = '';

    /**
     * Authenticate the admin against the admin guard, regenerate the session
     * and redirect to the dashboard. Rate-limited per username + IP.
     */
    public function login(): void
    {
        $this->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = 'admin-login:'.mb_strtolower($this->username).'|'.$this->throttleIp();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => 'تلاش‌های زیاد. '.ceil($seconds / 60).' دقیقه دیگر تلاش کنید.',
            ]);
        }

        if (! Auth::guard('admin')->attempt([
            'username' => $this->username,
            'password' => $this->password,
        ])) {
            RateLimiter::hit($throttleKey, 60);

            throw ValidationException::withMessages([
                'username' => 'نام کاربری یا رمز عبور اشتباه است.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        $this->regenerateSession();

        $this->redirect(route('admin.dashboard'), navigate: true);
    }

    /**
     * Regenerate the session id to prevent session fixation.
     */
    protected function regenerateSession(): void
    {
        session()->regenerate();
    }

    /**
     * Resolve the client IP used for rate limiting.
     */
    protected function throttleIp(): string
    {
        return request()->ip() ?? 'unknown';
    }

    public function render(): string
    {
        return 'livewire.admin.login';
    }
}
