<?php

namespace App\Livewire\Admin\Attributes;

use App\Livewire\Concerns\AuthorizesAdmin;
use App\Livewire\Concerns\DispatchesToasts;
use App\Models\ProductAttribute;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مدیریت ویژگی‌ها')]
class Index extends Component
{
    use AuthorizesAdmin;
    use DispatchesToasts;
    use WithPagination;

    public string $search = '';

    /**
     * @return LengthAwarePaginator<int, ProductAttribute>
     */
    #[Computed]
    public function attributeList(): LengthAwarePaginator
    {
        return ProductAttribute::query()
            ->withCount('values')
            ->when($this->search !== '', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $attributeId): void
    {
        $this->authorizeAdmin('attributes.delete');

        ProductAttribute::query()->findOrFail($attributeId)->delete();

        $this->flashToast('ویژگی حذف شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('attributes.view');

        return 'livewire.admin.attributes.index';
    }
}
