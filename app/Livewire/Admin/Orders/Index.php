<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('مدیریت سفارش‌ها')]
class Index extends Component
{
    use WithPagination;

    public string $status = '';

    public function authorizeAdmin(string $permission): void
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo($permission)) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    /**
     * @return LengthAwarePaginator<int, Order>
     */
    #[Computed]
    public function orders(): LengthAwarePaginator
    {
        return Order::query()
            ->with('user')
            ->when($this->status !== '', fn ($q) => $q->where('status', $this->status))
            ->orderByDesc('id')
            ->paginate(15);
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    /**
     * TailAdmin badge classes per order status.
     */
    public function statusColor(string $status): string
    {
        return match ($status) {
            'paid' => 'bg-success-light text-success',
            'shipped' => 'bg-info-light text-info',
            'delivered' => 'bg-success-light text-success',
            'cancelled' => 'bg-danger-light text-danger',
            default => 'bg-warning-light text-warning',
        };
    }

    /**
     * Status updates require the orders.update_status permission.
     */
    public function updateStatus(int $orderId, string $status): void
    {
        $this->authorizeAdmin('orders.update_status');

        $order = Order::query()->findOrFail($orderId);
        $order->update(['status' => $status]);

        if ($status === Order::STATUS_PAID && $order->paid_at === null) {
            $order->update(['paid_at' => now()]);
        }

        session()->flash('status', 'وضعیت سفارش به‌روزرسانی شد.');
    }

    public function render(): string
    {
        $this->authorizeAdmin('orders.view');

        return 'livewire.admin.orders.index';
    }
}
