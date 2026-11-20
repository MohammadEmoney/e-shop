<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-lg border border-danger-light bg-danger-light/50 px-4 py-3 text-sm text-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">مدیران</h2>
        @if (auth('admin')->user()->hasPermissionTo('admins.create'))
            <a href="{{ route('admin.admins.create') }}"
               class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                مدیر جدید
            </a>
        @endif
    </div>

    <div class="rounded-[10px] border border-stroke bg-white shadow-1 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-stroke bg-gray-4 text-left">
                    <th class="px-5 py-3 text-sm font-medium text-dark">نام کاربری</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">نام</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">نقش‌ها</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->admins as $adminRow)
                    <tr class="border-b border-stroke hover:bg-gray-4">
                        <td class="px-5 py-4 text-sm text-dark">{{ $adminRow->username }}</td>
                        <td class="px-5 py-4 text-sm text-dark">{{ $adminRow->name }}</td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach ($adminRow->roles as $role)
                                    <span class="rounded-full bg-gray-2 px-3 py-1 text-xs font-medium text-dark">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if (auth('admin')->user()->hasPermissionTo('admins.update'))
                                    <a href="{{ route('admin.admins.edit', $adminRow) }}"
                                       class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-primary hover:bg-primary hover:text-white">
                                        ویرایش
                                    </a>
                                @endif
                                @if (auth('admin')->user()->hasPermissionTo('admins.delete'))
                                    <button type="button"
                                            wire:click="delete({{ $adminRow->id }})"
                                            wire:confirm="این مدیر حذف شود؟"
                                            class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-danger hover:bg-danger hover:text-white">
                                        حذف
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-8 text-center text-sm text-[#64748B]">مدیری یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->admins->links() }}
    </div>
</div>