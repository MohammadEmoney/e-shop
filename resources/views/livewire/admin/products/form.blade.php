<div class="flex flex-col gap-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">{{ $product ? 'ویرایش محصول' : 'محصول جدید' }}</h2>
        <a href="{{ route('admin.products') }}" class="text-sm font-medium text-primary">بازگشت</a>
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
                <label class="mb-2.5 block text-sm font-medium text-dark">دسته</label>
                <select wire:model="category_id"
                        class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                    <option value="">انتخاب دسته</option>
                    @foreach (App\Models\Category::orderBy('name')->get() as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
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

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>