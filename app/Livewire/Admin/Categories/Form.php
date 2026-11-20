<?php

namespace App\Livewire\Admin\Categories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('دسته‌بندی')]
class Form extends Component
{
    public ?Category $category = null;

    public string $name = '';

    public string $slug = '';

    public ?int $parent_id = null;

    public string $description = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    public function mount(?Category $category = null): void
    {
        $this->authorizeAdmin($category ? 'categories.edit' : 'categories.create');

        $this->category = $category;

        if ($category) {
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->parent_id = $category->parent_id;
            $this->description = $category->description ?? '';
            $this->sort_order = $category->sort_order;
            $this->is_active = $category->is_active;
        }
    }

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    public function updatedName(): void
    {
        if (! $this->category) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->authorizeAdmin($this->category ? 'categories.edit' : 'categories.create');

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.($this->category->id ?? 'NULL')],
            'parent_id' => ['nullable', 'exists:categories,id', 'not_in:'.($this->category->id ?? '0')],
            'description' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if ($this->category) {
            $this->category->update($data);
        } else {
            Category::create($data);
        }

        session()->flash('status', 'دسته‌بندی ذخیره شد.');

        $this->redirectRoute('admin.categories', navigate: true);
    }

    /**
     * Categories available as a parent (excludes the category being edited
     * and its own descendants to avoid cycles).
     *
     * @return Collection<int, Category>
     */
    public function getParentOptionsProperty(): Collection
    {
        return Category::query()
            ->where('id', '!=', $this->category->id ?? 0)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function render(): string
    {
        return 'livewire.admin.categories.form';
    }
}
