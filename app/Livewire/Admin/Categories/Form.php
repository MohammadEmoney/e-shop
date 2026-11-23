<?php

namespace App\Livewire\Admin\Categories;

use App\Livewire\Concerns\AuthorizesAdmin;
use App\Livewire\Concerns\DispatchesToasts;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\ProductOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('دسته‌بندی')]
class Form extends Component
{
    use AuthorizesAdmin;
    use DispatchesToasts;

    public ?Category $category = null;

    public string $name = '';

    public string $slug = '';

    public ?int $parent_id = null;

    public string $description = '';

    public int $sort_order = 0;

    public bool $is_active = true;

    /**
     * IDs of the options (variant dimensions) attached to this category.
     *
     * @var array<int>
     */
    public array $selectedOptions = [];

    /**
     * IDs of the attributes (specifications) attached to this category.
     *
     * @var array<int>
     */
    public array $selectedAttributes = [];

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

            $this->selectedOptions = $category->options()->pluck('product_options.id')->all();
            $this->selectedAttributes = $category->attributes()->pluck('product_attributes.id')->all();
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
            'selectedOptions' => ['array'],
            'selectedOptions.*' => ['exists:product_options,id'],
            'selectedAttributes' => ['array'],
            'selectedAttributes.*' => ['exists:product_attributes,id'],
        ]);

        if ($this->category) {
            $this->category->update($data);
            $category = $this->category;
        } else {
            $category = Category::create($data);
        }

        // Attach the variant options and specification attributes for this category.
        $category->options()->sync($data['selectedOptions'] ?? []);
        $category->attributes()->sync($data['selectedAttributes'] ?? []);

        $this->flashToast('دسته‌بندی ذخیره شد.');

        $this->redirectRoute('admin.categories', navigate: true);
    }

    /**
     * Options (variant dimensions) available to attach to a category.
     *
     * @return Collection<int, ProductOption>
     */
    public function getAvailableOptionsProperty(): Collection
    {
        return ProductOption::query()
            ->withCount('values')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Attributes (specifications) available to attach to a category.
     *
     * @return Collection<int, ProductAttribute>
     */
    public function getAvailableAttributesProperty(): Collection
    {
        return ProductAttribute::query()
            ->withCount('values')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
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
