<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">دسته‌بندی‌ها</h2>
        @if (auth('admin')->user()->hasPermissionTo('categories.create'))
            <a href="{{ route('admin.categories.create') }}"
               class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                دسته‌بندی جدید
            </a>
        @endif
    </div>

    <div class="rounded-[10px] border border-stroke bg-white shadow-1 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-stroke bg-gray-4 text-left">
                    <th class="px-5 py-3 text-sm font-medium text-dark">نام</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">دسته مادر</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">وضعیت</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->categories as $category)
                    <tr class="border-b border-stroke hover:bg-gray-4">
                        <td class="px-5 py-4 text-sm text-dark">{{ $category->name }}</td>
                        <td class="px-5 py-4 text-sm text-[#64748B]">{{ $category->parent?->name ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $category->is_active ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                {{ $category->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if (auth('admin')->user()->hasPermissionTo('categories.edit'))
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-primary hover:bg-primary hover:text-white">
                                        ویرایش
                                    </a>
                                @endif
                                @if (auth('admin')->user()->hasPermissionTo('categories.delete'))
                                    <button type="button"
                                            wire:click="delete({{ $category->id }})"
                                            wire:confirm="این دسته‌بندی حذف شود؟"
                                            class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-danger hover:bg-danger hover:text-white">
                                        حذف
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-[#64748B]">دسته‌بندی یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>