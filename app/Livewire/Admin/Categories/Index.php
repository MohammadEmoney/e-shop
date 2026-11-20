<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('مدیریت دسته‌بندی‌ها')]
class Index extends Component
{
    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    /**
     * @return Collection<int, Category>
     */
    #[Computed]
    public function categories(): Collection
    {
        return Category::query()
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public function delete(int $categoryId): void
    {
        $this->authorizeAdmin('categories.delete');

        Category::query()->findOrFail($categoryId)->delete();

        session()->flash('status', 'دسته‌بندی حذف شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('categories.view');

        return 'livewire.admin.categories.index';
    }
}
