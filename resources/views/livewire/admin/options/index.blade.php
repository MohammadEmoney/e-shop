<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold text-dark">آپشن‌ها</h2>
        <div class="flex items-center gap-3">
            <input type="search" wire:model.live.debounce.300ms="search"
                   placeholder="جستجوی آپشن..."
                   class="rounded-lg border border-form-stroke bg-white px-4 py-2.5 text-sm outline-none focus:border-primary">
            @if (auth('admin')->user()->hasPermissionTo('options.create'))
                <a href="{{ route('admin.options.create') }}"
                   class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                    آپشن جدید
                </a>
            @endif
        </div>
    </div>

    <div class="rounded-[10px] border border-stroke bg-white shadow-1 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-stroke bg-gray-4 text-left">
                    <th class="px-5 py-3 text-sm font-medium text-dark">نام</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">نوع</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">تعداد مقادیر</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">وضعیت</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->options as $option)
                    <tr class="border-b border-stroke hover:bg-gray-4">
                        <td class="px-5 py-4 text-sm text-dark">{{ $option->name }}</td>
                        <td class="px-5 py-4 text-sm text-[#64748B]">{{ $option->type }}</td>
                        <td class="px-5 py-4 text-sm text-[#64748B]">{{ $option->values_count }}</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $option->is_active ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                {{ $option->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if (auth('admin')->user()->hasPermissionTo('options.edit'))
                                    <a href="{{ route('admin.options.edit', $option) }}"
                                       class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-primary hover:bg-primary hover:text-white">
                                        ویرایش
                                    </a>
                                @endif
                                @if (auth('admin')->user()->hasPermissionTo('options.delete'))
                                    <button type="button"
                                            wire:click="delete({{ $option->id }})"
                                            wire:confirm="این آپشن حذف شود؟"
                                            class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-danger hover:bg-danger hover:text-white">
                                        حذف
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-[#64748B]">آپشنی یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->options->links() }}
    </div>
</div>