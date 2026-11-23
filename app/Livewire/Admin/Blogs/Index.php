<?php

namespace App\Livewire\Admin\Blogs;

use App\Livewire\Concerns\DispatchesToasts;
use App\Models\Blog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مدیریت مقالات')]
class Index extends Component
{
    use DispatchesToasts;
    use WithPagination;

    public string $search = '';

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    /**
     * @return LengthAwarePaginator<int, Blog>
     */
    #[Computed]
    public function blogs(): LengthAwarePaginator
    {
        return Blog::query()
            ->when($this->search !== '', fn ($q) => $q->where('title', 'like', '%'.$this->search.'%'))
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(int $blogId): void
    {
        $this->authorizeAdmin('blogs.delete');

        Blog::query()->findOrFail($blogId)->delete();

        $this->flashToast('مقاله حذف شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('blogs.view');

        return 'livewire.admin.blogs.index';
    }
}
