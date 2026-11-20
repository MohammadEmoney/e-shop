<?php

namespace App\Livewire\Admin\Admins;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مدیریت مدیران')]
class Index extends Component
{
    use WithPagination;

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    /**
     * @return LengthAwarePaginator<int, Admin>
     */
    #[Computed]
    public function admins(): LengthAwarePaginator
    {
        return Admin::query()
            ->with('roles')
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function delete(int $adminId): void
    {
        $this->authorizeAdmin('admins.delete');

        $target = Admin::query()->findOrFail($adminId);

        // Prevent deleting yourself.
        if (Auth::guard('admin')->id() === $adminId) {
            session()->flash('error', 'امکان حذف حساب خودتان وجود ندارد.');

            return;
        }

        $target->delete();

        session()->flash('status', 'مدیر حذف شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('admins.view');

        return 'livewire.admin.admins.index';
    }
}
