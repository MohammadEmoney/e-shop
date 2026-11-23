<?php

namespace App\Livewire\Admin\Options;

use App\Livewire\Concerns\AuthorizesAdmin;
use App\Livewire\Concerns\DispatchesToasts;
use App\Models\ProductOption;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مدیریت آپشن‌ها')]
class Index extends Component
{
    use AuthorizesAdmin;
    use DispatchesToasts;
    use WithPagination;

    public string $search = '';

    /**
     * @return LengthAwarePaginator<int, ProductOption>
     */
    #[Computed]
    public function options(): LengthAwarePaginator
    {
        return ProductOption::query()
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

    public function delete(int $optionId): void
    {
        $this->authorizeAdmin('options.delete');

        ProductOption::query()->findOrFail($optionId)->delete();

        $this->flashToast('آپشن حذف شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('options.view');

        return 'livewire.admin.options.index';
    }
}
