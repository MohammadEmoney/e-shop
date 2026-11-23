<div class="flex flex-col gap-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">{{ $option ? 'ویرایش آپشن' : 'آپشن جدید' }}</h2>
        <a href="{{ route('admin.options') }}" class="text-sm font-medium text-primary">بازگشت</a>
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
                <label class="mb-2.5 block text-sm font-medium text-dark">نوع</label>
                <select wire:model="type"
                        class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                    <option value="select">انتخابی</option>
                    <option value="color">رنگ</option>
                </select>
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">ترتیب</label>
                <input type="number" min="0" step="1" wire:model="sort_order"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('sort_order') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5 flex items-center gap-3">
            <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-dark">
                <input type="checkbox" wire:model="is_active" class="h-4 w-4">
                فعال
            </label>
        </div>

        {{-- Values --}}
        <div class="mt-6">
            <div class="mb-3 flex items-center justify-between">
                <h3 class="text-base font-semibold text-dark">مقادیر</h3>
                <button type="button" wire:click="addValue"
                        class="rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-dark">
                    + افزودن مقدار
                </button>
            </div>

            @forelse ($values as $index => $row)
                <div class="mb-2 flex items-center gap-2">
                    <input type="text" wire:model="values.{{ $index }}.value" placeholder="مقدار (مثلاً Medium)"
                           class="w-full rounded-lg border border-form-stroke bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                    <input type="text" wire:model="values.{{ $index }}.slug" placeholder="اسلاگ (مثلاً m)"
                           class="w-full rounded-lg border border-form-stroke bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                    <button type="button" wire:click="removeValue({{ $index }})"
                            class="rounded-lg bg-danger px-3 py-2 text-sm text-white hover:opacity-90">
                        حذف
                    </button>
                </div>
            @empty
                <p class="text-sm text-[#99A2B7]">هیچ مقداری تعریف نشده است.</p>
            @endforelse
            @error('values') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>