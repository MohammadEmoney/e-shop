<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مدیریت محصولات')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * The admin guard is resolved explicitly so authorization never leaks to
     * the default (web) guard.
     */
    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    /**
     * @return LengthAwarePaginator<int, Product>
     */
    #[Computed]
    public function products(): LengthAwarePaginator
    {
        return Product::query()
            ->with('category')
            ->when($this->search !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('sku', 'like', '%'.$this->search.'%')))
            ->orderByDesc('id')
            ->paginate(10);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $productId): void
    {
        $this->authorizeAdmin('products.delete');

        Product::query()->findOrFail($productId)->delete();

        session()->flash('status', 'محصول حذف شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('products.view');

        return 'livewire.admin.products.index';
    }
}
