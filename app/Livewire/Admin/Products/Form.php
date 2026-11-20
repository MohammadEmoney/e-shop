<?php

namespace App\Livewire\Admin\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('محصول')]
class Form extends Component
{
    public ?Product $product = null;

    public string $name = '';

    public string $slug = '';

    public ?int $category_id = null;

    public string $short_description = '';

    public string $description = '';

    public int $price = 0;

    public ?int $compare_at_price = null;

    public int $stock = 0;

    public ?string $sku = null;

    public bool $is_active = true;

    public function mount(?Product $product = null): void
    {
        $this->authorizeAdmin($product ? 'products.edit' : 'products.create');

        $this->product = $product;

        if ($product) {
            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->category_id = $product->category_id;
            $this->short_description = $product->short_description ?? '';
            $this->description = $product->description;
            $this->price = $product->price;
            $this->compare_at_price = $product->compare_at_price;
            $this->stock = $product->stock;
            $this->sku = $product->sku;
            $this->is_active = $product->is_active;
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
        if (! $this->product) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->authorizeAdmin($this->product ? 'products.edit' : 'products.create');

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:products,slug,'.($this->product->id ?? 'NULL')],
            'category_id' => ['required', 'exists:categories,id'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'compare_at_price' => ['nullable', 'integer', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku,'.($this->product->id ?? 'NULL')],
            'is_active' => ['boolean'],
        ]);

        if ($this->product) {
            $this->product->update($data);
            $product = $this->product;
        } else {
            $product = Product::create($data);
        }

        // Keep the denormalized AI search text in sync.
        $product->forceFill([
            'search_text' => implode(' ', array_filter([
                $product->name,
                $product->short_description,
                $product->description,
                $product->category?->name,
            ])),
        ])->save();

        session()->flash('status', 'محصول ذخیره شد.');

        $this->redirectRoute('admin.products', navigate: true);
    }

    public function render(): string
    {
        return 'livewire.admin.products.form';
    }
}
