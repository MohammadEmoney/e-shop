<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-xl font-bold text-dark">سفارش‌ها</h2>
        <select wire:model.live="status"
                class="rounded-lg border border-form-stroke bg-white px-4 py-2.5 text-sm outline-none focus:border-primary">
            <option value="">همه وضعیت‌ها</option>
            <option value="pending">در انتظار</option>
            <option value="paid">پرداخت شده</option>
            <option value="shipped">ارسال شده</option>
            <option value="delivered">تحویل شده</option>
            <option value="cancelled">لغو شده</option>
        </select>
    </div>

    <div class="rounded-[10px] border border-stroke bg-white shadow-1 overflow-x-auto">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-stroke bg-gray-4 text-left">
                    <th class="px-5 py-3 text-sm font-medium text-dark">شماره سفارش</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">مشتری</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">مبلغ (تومان)</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">وضعیت</th>
                    <th class="px-5 py-3 text-sm font-medium text-dark">تاریخ</th>
                    @if (auth('admin')->user()->hasPermissionTo('orders.update_status'))
                        <th class="px-5 py-3 text-sm font-medium text-dark">تغییر وضعیت</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($this->orders as $order)
                    <tr class="border-b border-stroke hover:bg-gray-4">
                        <td class="px-5 py-4 text-sm text-dark">{{ $order->order_number }}</td>
                        <td class="px-5 py-4 text-sm text-dark">{{ $order->user?->name ?? $order->user?->phone ?? '—' }}</td>
                        <td class="px-5 py-4 text-sm text-dark">{{ number_format($order->total) }}</td>
                        <td class="px-5 py-4 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-medium {{ $this->statusColor($order->status) }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm text-[#64748B]">{{ $order->created_at?->format('Y/m/d') }}</td>
                        @if (auth('admin')->user()->hasPermissionTo('orders.update_status'))
                            <td class="px-5 py-4">
                                <select wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                        class="rounded-lg border border-form-stroke bg-white px-3 py-1.5 text-sm outline-none focus:border-primary">
                                    @foreach (['pending', 'paid', 'shipped', 'delivered', 'cancelled'] as $s)
                                        <option value="{{ $s }}" @selected($order->status === $s)>{{ $s }}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-sm text-[#64748B]">سفارشی یافت نشد.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $this->orders->links() }}
    </div>
</div>