<?php

namespace App\Livewire\Admin\Admins;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
#[Title('مدیر')]
class Form extends Component
{
    public ?Admin $admin = null;

    public string $username = '';

    public string $name = '';

    public string $password = '';

    /** @var list<int> */
    public array $roles = [];

    public function mount(?Admin $admin = null): void
    {
        $this->authorizeAdmin($admin ? 'admins.update' : 'admins.create');

        $this->admin = $admin;

        if ($admin) {
            $this->username = $admin->username;
            $this->name = $admin->name;
            $this->roles = array_values(array_map('intval', $admin->roles()->pluck('id')->all()));
        }
    }

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    public function save(): void
    {
        $this->authorizeAdmin($this->admin ? 'admins.update' : 'admins.create');

        $data = $this->validate([
            'username' => ['required', 'string', 'max:255', 'unique:admins,username,'.($this->admin->id ?? 'NULL')],
            'name' => ['required', 'string', 'max:255'],
            'password' => [$this->admin ? 'nullable' : 'required', 'string', 'min:8'],
            'roles' => ['array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $payload = [
            'username' => $data['username'],
            'name' => $data['name'],
        ];

        // Only set a new password when provided.
        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        if ($this->admin) {
            $this->admin->update($payload);
            $admin = $this->admin;
        } else {
            $admin = Admin::create($payload);
        }

        // Assign roles via syncRoles (maps role ids to names under the admin guard).
        $roleNames = Role::query()->whereKey($this->roles)->pluck('name');
        $admin->syncRoles($roleNames->all());

        session()->flash('status', 'مدیر ذخیره شد.');

        $this->redirectRoute('admin.admins', navigate: true);
    }

    public function render(): string
    {
        return 'livewire.admin.admins.form';
    }
}
