<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Concerns\AuthorizesAdmin;
use App\Livewire\Concerns\DispatchesToasts;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Services\VariantGenerator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
#[Title('محصول')]
class Form extends Component
{
    use AuthorizesAdmin;
    use DispatchesToasts;
    use WithFileUploads;

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

    /** @var UploadedFile|array<int, UploadedFile>|null */
    public $uploads = null;

    /** @var UploadedFile|null Main image upload. */
    public $mainImageUpload = null;

    /**
     * Variant drafts for the product form.
     *
     * @var array<int, array{id: int|null, name: string, sku: string|null, price: int, stock: int, is_active: bool, slug: string|null, option_value_ids: array<int>|null}>
     */
    public array $variants = [];

    /**
     * Selected option values grouped by option id, used to generate variants.
     *
     * @var array<int, array<int>>
     */
    public array $selectedOptions = [];

    /**
     * Selected attribute value ids (specifications) for the product.
     *
     * @var array<int>
     */
    public array $selectedAttributeValues = [];

    /**
     * Variant drafts generated from the selected options.
     *
     * @var list<array{name: string, slug: string, price: int, stock: int, is_active: bool, option_value_ids: array<int>}>
     */
    public array $generatedVariants = [];

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

            // Rebuild the option selection from the saved variants so the UI
            // reflects how the current variants were combined.
            $this->selectedOptions = $this->rebuildSelectedOptions($product);

            // Load the product's chosen specification values.
            $this->selectedAttributeValues = $product->attributeValues()->pluck('product_attribute_values.id')->all();

            $this->variants = array_values(
                $product->variants()->orderBy('id')->get()
                    ->map(fn ($v) => [
                        'id' => $v->id,
                        'name' => $v->name,
                        'slug' => $v->slug,
                        'sku' => $v->sku,
                        'price' => $v->price,
                        'stock' => $v->stock,
                        'is_active' => $v->is_active,
                        'option_value_ids' => $v->option_value_ids,
                    ])->all()
            );
        }
    }

    public function addVariant(): void
    {
        $this->variants[] = [
            'id' => null,
            'name' => '',
            'slug' => null,
            'sku' => null,
            'price' => $this->price,
            'stock' => 0,
            'is_active' => true,
            'option_value_ids' => null,
        ];
    }

    public function removeVariant(int $index): void
    {
        unset($this->variants[$index]);
        $this->variants = array_values($this->variants);
    }

    /**
     * The options available for the selected category.
     *
     * @return Collection<int, ProductOption>
     */
    public function getProductOptionsProperty(): Collection
    {
        if (! $this->category_id) {
            return new Collection;
        }

        return Category::query()->find($this->category_id)?->options()->with('values')->get()
            ?? new Collection;
    }

    /**
     * The attributes (specifications) available for the selected category.
     *
     * @return Collection<int, ProductAttribute>
     */
    public function getProductAttributesProperty(): Collection
    {
        if (! $this->category_id) {
            return new Collection;
        }

        return Category::query()->find($this->category_id)?->attributes()->with('values')->get()
            ?? new Collection;
    }

    /**
     * When the category changes, reset the option/attribute selection and generated variants.
     */
    public function updatedCategoryId(): void
    {
        $this->selectedOptions = [];
        $this->selectedAttributeValues = [];
        $this->generatedVariants = [];
    }

    /**
     * Regenerate variant drafts whenever the selected option values change.
     */
    public function updatedSelectedOptions(): void
    {
        $this->generateVariants();
    }

    /**
     * Rebuild the selected-option-value map from a product's saved variants.
     *
     * @return array<int, list<int>>
     */
    protected function rebuildSelectedOptions(Product $product): array
    {
        $grouped = [];

        foreach ($product->variants as $variant) {
            foreach ($variant->optionValues as $value) {
                $grouped[$value->product_option_id][$value->id] = $value->id;
            }
        }

        return array_map('array_values', $grouped);
    }

    /**
     * Build variant drafts from the selected option values using the cartesian product.
     */
    public function generateVariants(): void
    {
        $this->generatedVariants = [];

        /** @var array<int|string, array<int, ProductOptionValue>> $grouped */
        $grouped = [];
        foreach ($this->selectedOptions as $optionId => $valueIds) {
            $values = ProductOptionValue::query()->whereIn('id', $valueIds ?: [])->get();
            if ($values->isNotEmpty()) {
                $grouped[(string) $optionId] = $values->all();
            }
        }

        if (empty($grouped)) {
            return;
        }

        $this->generatedVariants = app(VariantGenerator::class)->generate(
            $grouped,
            $this->price,
            $this->stock,
            $this->slug ?: Str::slug($this->name),
        );
    }

    public function updatedName(): void
    {
        if (! $this->product) {
            $this->slug = Str::slug($this->name);
        }
    }

    /**
     * Set the given gallery media as the product's main image.
     */
    public function setMainImage(int $mediaId): void
    {
        $this->authorizeAdmin($this->product ? 'products.edit' : 'products.create');

        if (! $this->product) {
            return;
        }

        $media = $this->product->getMedia('images')->firstWhere('id', $mediaId);

        if ($media) {
            // Remove any existing main image, then promote the chosen one.
            $this->product->clearMediaCollection('main_image');
            $this->product->media()->find($mediaId)?->move($this->product, 'main_image');
        }

        $this->flashToast('تصویر اصلی به‌روزرسانی شد.');
    }

    /**
     * Delete the product's main image.
     */
    public function removeMainImage(): void
    {
        $this->authorizeAdmin($this->product ? 'products.edit' : 'products.create');

        if ($this->product) {
            $this->product->clearMediaCollection('main_image');
        }

        $this->flashToast('تصویر اصلی حذف شد.');
    }

    /**
     * Remove a temporary (not-yet-saved) uploaded file from the form state.
     */
    public function removeTempUpload(string $modelKey, int $index): void
    {
        if ($modelKey === 'uploads' && is_array($this->uploads) && isset($this->uploads[$index])) {
            unset($this->uploads[$index]);
            $this->uploads = array_values($this->uploads);
        } elseif ($modelKey === 'mainImageUpload') {
            $this->mainImageUpload = null;
        }

        $this->flashToast('فایل انتخاب‌شده حذف شد.');
    }

    /**
     * Delete an existing media item from the model.
     */
    public function removeMedia(int $mediaId): void
    {
        $this->authorizeAdmin($this->product ? 'products.edit' : 'products.create');

        if ($this->product) {
            $this->product->deleteMedia($mediaId);
        }

        $this->flashToast('فایل حذف شد.');
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

        // Sync the product's specification (attribute) values.
        $product->attributeValues()->sync($this->selectedAttributeValues);

        // Attach any files uploaded through the dropzone to the images collection.
        if ($this->uploads) {
            $files = is_array($this->uploads) ? $this->uploads : [$this->uploads];

            foreach ($files as $file) {
                $source = $this->resolveUploadedSource($file);
                if ($source !== null) {
                    $product->addMedia($source)
                        ->toMediaCollection('images');
                }
            }
        }

        // Attach the main image (if provided) to the main_image collection.
        if ($this->mainImageUpload) {
            $source = $this->resolveUploadedSource($this->mainImageUpload);
            if ($source !== null) {
                $product->clearMediaCollection('main_image');
                $product->addMedia($source)->toMediaCollection('main_image');
            }
        }
        // Merge the manually-managed variants with the option-generated drafts.
        $allVariants = $this->variants;
        foreach ($this->generatedVariants as $gen) {
            $allVariants[] = $gen;
        }

        // Sync variants (create new, update existing, delete removed).
        $keepIds = [];
        foreach ($allVariants as $variant) {
            $data = [
                'name' => $variant['name'],
                'slug' => $variant['slug'] ?? null,
                'sku' => $variant['sku'] ?? null,
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'is_active' => $variant['is_active'],
                'option_value_ids' => $variant['option_value_ids'] ?? null,
            ];
            if (! empty($variant['id'])) {
                $model = $product->variants()->find($variant['id']);
                if ($model) {
                    $model->update($data);
                    $keepIds[] = $model->id;
                    if (! empty($variant['option_value_ids'] ?? null)) {
                        $model->optionValues()->sync($variant['option_value_ids']);
                    }

                    continue;
                }
            }
            $model = $product->variants()->create($data);
            $keepIds[] = $model->id;
            if (! empty($variant['option_value_ids'] ?? null)) {
                $model->optionValues()->sync($variant['option_value_ids']);
            }
        }
        $product->variants()->whereNotIn('id', $keepIds)->delete();

        $this->flashToast('محصول ذخیره شد.');

        $this->redirectRoute('admin.products', navigate: true);
    }

    /**
     * Resolve a path (or UploadedFile) acceptable by Spatie media's addMedia(),
     * regardless of whether the source is a Livewire TemporaryUploadedFile or a
     * regular Illuminate UploadedFile.
     *
     * @return UploadedFile|string|null
     */
    protected function resolveUploadedSource(UploadedFile|string $file)
    {
        if (is_string($file)) {
            return $file;
        }

        return $file->getRealPath();
    }

    public function render(): string
    {
        return 'livewire.admin.products.form';
    }
}
