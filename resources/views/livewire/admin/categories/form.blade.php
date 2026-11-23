<div class="flex flex-col gap-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">{{ $category ? 'ویرایش دسته‌بندی' : 'دسته‌بندی جدید' }}</h2>
        <a href="{{ route('admin.categories') }}" class="text-sm font-medium text-primary">بازگشت</a>
    </div>

    <form wire:submit="save" class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
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

            <div>
                <x-select2
                    :model="'parent_id'"
                    label="دسته مادر"
                    placeholder="بدون دسته مادر"
                >
                    @foreach ($this->parentOptions as $option)
                        <option value="{{ $option->id }}" @selected((string) $parent_id === (string) $option->id)>{{ $option->name }}</option>
                    @endforeach
                </x-select2>
                @error('parent_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">ترتیب</label>
                <input type="number" min="0" step="1" wire:model="sort_order"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('sort_order') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-2.5 block text-sm font-medium text-dark">توضیحات</label>
            <textarea wire:model="description" rows="4"
                      class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary"></textarea>
            @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5 flex items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-dark">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4">
                فعال
            </label>
        </div>

        {{-- Options (variant dimensions) attached to this category --}}
        <div class="mt-6">
            <h3 class="mb-1 text-base font-semibold text-dark">آپشن‌ها (ابعاد تنوع)</h3>
            <p class="mb-3 text-xs text-[#99A2B7]">آپشن‌های انتخابی هنگام ساخت محصول در این دسته نمایش داده می‌شوند.</p>

            @forelse ($this->availableOptions as $option)
                <label class="mb-1.5 flex cursor-pointer items-center gap-2 text-sm text-dark">
                    <input type="checkbox" wire:model="selectedOptions" value="{{ $option->id }}" class="h-4 w-4">
                    {{ $option->name }}
                    <span class="text-xs text-[#99A2B7]">({{ $option->values_count ?? $option->values()->count() }} مقدار)</span>
                </label>
            @empty
                <p class="text-sm text-[#99A2B7]">هنوز آپشنی تعریف نشده است.</p>
            @endforelse
            @error('selectedOptions') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        {{-- Attributes (specifications) attached to this category --}}
        <div class="mt-6">
            <h3 class="mb-1 text-base font-semibold text-dark">ویژگی‌ها (مشخصات)</h3>
            <p class="mb-3 text-xs text-[#99A2B7]">ویژگی‌های انتخابی هنگام ساخت محصول در این دسته نمایش داده می‌شوند.</p>

            @forelse ($this->availableAttributes as $attribute)
                <label class="mb-1.5 flex cursor-pointer items-center gap-2 text-sm text-dark">
                    <input type="checkbox" wire:model="selectedAttributes" value="{{ $attribute->id }}" class="h-4 w-4">
                    {{ $attribute->name }}
                    <span class="text-xs text-[#99A2B7]">({{ $attribute->values_count ?? $attribute->values()->count() }} مقدار)</span>
                </label>
            @empty
                <p class="text-sm text-[#99A2B7]">هنوز ویژگی‌ای تعریف نشده است.</p>
            @endforelse
            @error('selectedAttributes') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>