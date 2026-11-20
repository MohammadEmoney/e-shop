<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold text-dark">محصولات</h2>
        <div class="flex items-center gap-3">
            <input type="search"
                   wire:model.live.debounce.300ms="search"
                   placeholder="جستجوی محصول..."
                   class="rounded-lg border border-form-stroke bg-white px-4 py-2.5 text-sm outline-none focus:border-primary">
            @if (auth('admin')->user()->hasPermissionTo('products.create'))
                <a href="{{ route('admin.products.create') }}"
                   class="rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-dark">
                    محصول جدید
                </a>
            @endif
        </div>
    </div>

    <div class="rounded-[10px] border border-stroke bg-white shadow-1 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-stroke bg-gray-4 text-left">
                    <th class="px-5 py-3 text-sm font-medium text-dark">نام</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">دسته</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">قیمت (تومان)</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">موجودی</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">وضعیت</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->products as $product)
                    <tr class="border-b border-stroke hover:bg-gray-4">
                        <td class="px-5 py-4 text-sm text-dark">{{ $product->name }}</td>
                        <td class="px-5 py-4 text-sm text-[#64748B]">{{ $product->category?->name }}</td>
                        <td class="px-5 py-4 text-sm text-dark">{{ number_format($product->price) }}</td>
                        <td class="px-5 py-4 text-sm text-dark">{{ $product->stock }}</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $product->is_active ? 'bg-success-light text-success' : 'bg-danger-light text-danger' }}">
                                {{ $product->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                @if (auth('admin')->user()->hasPermissionTo('products.edit'))
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-primary hover:bg-primary hover:text-white">
                                        ویرایش
                                    </a>
                                @endif
                                @if (auth('admin')->user()->hasPermissionTo('products.delete'))
                                    <button type="button"
                                            wire:click="delete({{ $product->id }})"
                                            wire:confirm="این محصول حذف شود؟"
                                            class="rounded-lg border border-stroke px-3 py-1.5 text-sm text-danger hover:bg-danger hover:text-white">
                                        حذف
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-[#64748B]">محصولی یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->products->links() }}
    </div>
</div>