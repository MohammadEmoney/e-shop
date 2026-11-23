<div class="flex flex-col gap-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">{{ $product ? 'ویرایش محصول' : 'محصول جدید' }}</h2>
        <a href="{{ route('admin.products') }}" class="text-sm font-medium text-primary">بازگشت</a>
    </div>

    <form wire:submit="save" class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
        {{-- Main image uploader (single) --}}
        <div class="mb-6">
            <x-media-uploader
                :model="$product"
                collection="main_image"
                label="تصویر اصلی"
                accept="image/jpeg,image/png,image/webp,image/gif"
                :multiple="false"
                modelKey="mainImageUpload"
                :showMainControl="false"
                :tempUploads="$mainImageUpload ? [$mainImageUpload] : []"
            />
        </div>

        {{-- Gallery uploader (multiple images) --}}
        <div class="mb-6">
            <x-media-uploader
                :model="$product"
                collection="images"
                label="گالری تصاویر"
                accept="image/jpeg,image/png,image/webp,image/gif"
                :multiple="true"
                modelKey="uploads"
                :showMainControl="true"
                :tempUploads="$uploads ? (is_array($uploads) ? $uploads : [$uploads]) : []"
            />
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">نام</label>
                <input type="text" wire:model="name"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">اسلاگ</label>
                <input type="text" wire:model="slug"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('slug') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div wire:ignore>
                <x-select2
                    wire:ignore.self
                    :model="'category_id'"
                    label="دسته"
                    placeholder="انتخاب دسته"
                >
                    @foreach (App\Models\Category::orderBy('name')->get() as $cat)
                        <option value="{{ $cat->id }}" @selected((string) $category_id === (string) $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </x-select2>
                @error('category_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">قیمت (تومان)</label>
                <input type="number" min="0" step="1" wire:model="price"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('price') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">قیمت قبل از تخفیف (تومان)</label>
                <input type="number" min="0" step="1" wire:model="compare_at_price"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('compare_at_price') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">موجودی</label>
                <input type="number" min="0" step="1" wire:model="stock"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('stock') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">کد محصول (SKU)</label>
                <input type="text" wire:model="sku"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('sku') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-2.5 block text-sm font-medium text-dark">توضیح کوتاه</label>
            <textarea wire:model="short_description" rows="2"
                      class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary"></textarea>
            @error('short_description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5">
            <label class="mb-2.5 block text-sm font-medium text-dark">توضیحات کامل</label>
            <textarea wire:model="description" rows="5"
                      class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary"></textarea>
            @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5 flex items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-dark">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4">
                فعال
            </label>
        </div>

        {{-- Options (variant dimensions) from the selected category --}}
        <div class="mt-6 rounded-[10px] border border-stroke bg-gray-4/40 p-5">
            <h3 class="mb-1 text-base font-semibold text-dark">آپشن‌ها (ساخت واریانت)</h3>
            <p class="mb-4 text-xs text-[#99A2B7]">با انتخاب مقادیر، واریانت‌های ترکیبی به‌صورت خودکار ساخته می‌شوند.</p>

            @if (! $category_id)
                <p class="text-sm text-[#99A2B7]">ابتدا یک دسته انتخاب کنید تا آپشن‌های آن نمایش داده شود.</p>
            @elseif ($this->productOptions->isEmpty())
                <p class="text-sm text-[#99A2B7]">برای این دسته آپشنی تعریف نشده است.</p>
            @else
                @foreach ($this->productOptions as $option)
                    <div class="mb-5 last:mb-0">
                        <p class="mb-2 text-sm font-semibold text-dark">{{ $option->name }}</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($option->values as $value)
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-dark">
                                    <input type="checkbox"
                                           wire:model="selectedOptions.{{ $option->id }}"
                                           value="{{ $value->id }}"
                                           class="h-4 w-4">
                                    {{ $value->value }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @if (! empty($generatedVariants))
                    <div class="mt-4 rounded-lg border border-success-light bg-white p-4">
                        <p class="mb-2 text-sm font-semibold text-success">{{ count($generatedVariants) }} واریانت ساخته شد.</p>
                        <div class="space-y-1.5">
                            @foreach ($generatedVariants as $index => $variant)
                                <div class="grid grid-cols-12 items-center gap-2 rounded-md border border-stroke p-2">
                                    <div class="col-span-6 text-sm text-dark">{{ $variant['name'] }}</div>
                                    <div class="col-span-2">
                                        <input type="number" min="0" wire:model="generatedVariants.{{ $index }}.price"
                                               class="w-full rounded-lg border border-form-stroke bg-white px-2 py-1.5 text-sm outline-none focus:border-primary">
                                    </div>
                                    <div class="col-span-2">
                                        <input type="number" min="0" wire:model="generatedVariants.{{ $index }}.stock"
                                               class="w-full rounded-lg border border-form-stroke bg-white px-2 py-1.5 text-sm outline-none focus:border-primary">
                                    </div>
                                    <div class="col-span-2 text-left text-xs text-[#99A2B7]">{{ $variant['slug'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Attributes (specifications) from the selected category --}}
        <div class="mt-6">
            <h3 class="mb-1 text-base font-semibold text-dark">ویژگی‌ها (مشخصات)</h3>
            <p class="mb-3 text-xs text-[#99A2B7]">مقادیر مشخصات این محصول را انتخاب کنید.</p>

            @if (! $category_id)
                <p class="text-sm text-[#99A2B7]">ابتدا یک دسته انتخاب کنید تا ویژگی‌های آن نمایش داده شود.</p>
            @elseif ($this->productAttributes->isEmpty())
                <p class="text-sm text-[#99A2B7]">برای این دسته ویژگی‌ای تعریف نشده است.</p>
            @else
                @foreach ($this->productAttributes as $attribute)
                    <div class="mb-4">
                        <p class="mb-2 text-sm font-semibold text-dark">{{ $attribute->name }}</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach ($attribute->values as $value)
                                <label class="flex cursor-pointer items-center gap-2 text-sm text-dark">
                                    <input type="checkbox"
                                           wire:model="selectedAttributeValues"
                                           value="{{ $value->id }}"
                                           class="h-4 w-4">
                                    {{ $value->value }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Variants (manual) --}}
        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-base font-semibold text-dark">تنوع محصول (واریانت‌ها)</h3>
                <button type="button" wire:click="addVariant"
                        class="rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-dark">
                    + افزودن واریانت
                </button>
            </div>

            @forelse ($variants as $index => $variant)
                <div class="mb-3 grid grid-cols-12 items-center gap-2 rounded-lg border border-stroke bg-gray-4 p-3">
                    <div class="col-span-3">
                        <input type="text" wire:model="variants.{{ $index }}.name" placeholder="نام (مثلاً قرمز / L)"
                               class="w-full rounded-lg border border-form-stroke bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                    </div>
                    <div class="col-span-2">
                        <input type="text" wire:model="variants.{{ $index }}.sku" placeholder="SKU"
                               class="w-full rounded-lg border border-form-stroke bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                    </div>
                    <div class="col-span-2">
                        <input type="number" min="0" step="1" wire:model="variants.{{ $index }}.price" placeholder="قیمت"
                               class="w-full rounded-lg border border-form-stroke bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                    </div>
                    <div class="col-span-2">
                        <input type="number" min="0" step="1" wire:model="variants.{{ $index }}.stock" placeholder="موجودی"
                               class="w-full rounded-lg border border-form-stroke bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                    </div>
                    <div class="col-span-1">
                        <input type="checkbox" wire:model="variants.{{ $index }}.is_active" class="h-4 w-4" title="فعال">
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <button type="button" wire:click="removeVariant({{ $index }})"
                                class="rounded-lg bg-danger px-3 py-1.5 text-sm text-white hover:opacity-90">
                            حذف
                        </button>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[#99A2B7]">هیچ واریانتی تعریف نشده است.</p>
            @endforelse
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>

