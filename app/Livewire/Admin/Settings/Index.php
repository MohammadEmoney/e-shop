<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('تنظیمات سایت')]
class Index extends Component
{
    public string $site_name = '';

    public string $contact_phone = '';

    public string $contact_email = '';

    public string $address = '';

    public function mount(): void
    {
        $this->authorizeAdmin('settings.view');

        $this->site_name = Setting::where('key', 'site_name')->value('value') ?? '';
        $this->contact_phone = Setting::where('key', 'contact_phone')->value('value') ?? '';
        $this->contact_email = Setting::where('key', 'contact_email')->value('value') ?? '';
        $this->address = Setting::where('key', 'address')->value('value') ?? '';
    }

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    public function save(): void
    {
        $this->authorizeAdmin('settings.update');

        $data = $this->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $this->setSetting('site_name', $data['site_name']);
        $this->setSetting('contact_phone', $data['contact_phone']);
        $this->setSetting('contact_email', $data['contact_email']);
        $this->setSetting('address', $data['address']);

        session()->flash('status', 'تنظیمات ذخیره شد.');
    }

    protected function setSetting(string $key, ?string $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'general']);
    }

    public function render(): string
    {
        return 'livewire.admin.settings.index';
    }
}
