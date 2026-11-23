<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('داشبورد مدیریت')]
class Dashboard extends Component
{
    public function render(): string
    {
        if (! Auth::guard('admin')->user()?->hasPermissionTo('dashboard.view')) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return 'livewire.admin.dashboard';
    }

    /**
     * Aggregated metrics for the dashboard.
     *
     * @return array<string, int>
     */
    public function getStatsProperty(): array
    {
        return [
            'products' => Product::count(),
            'categories' => Category::count(),
            'orders' => Order::count(),
            'customers' => User::count(),
            'admins' => Admin::count(),
        ];
    }

    /**
     * Data for the ApexCharts pie chart (orders grouped by status).
     *
     * @return array{labels: list<string>, series: list<int>}
     */
    public function getOrderStatusChartProperty(): array
    {
        $orders = Order::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = [
            'pending' => 'در انتظار',
            'paid' => 'پرداخت شده',
            'shipped' => 'ارسال شده',
            'delivered' => 'تحویل شده',
            'cancelled' => 'لغو شده',
        ];

        $series = [];
        $labelsOut = [];

        foreach ($labels as $status => $label) {
            if (($orders[$status] ?? 0) > 0) {
                $labelsOut[] = $label;
                $series[] = (int) $orders[$status];
            }
        }

        if (empty($series)) {
            $labelsOut = ['بدون سفارش'];
            $series = [1];
        }

        return ['labels' => $labelsOut, 'series' => $series];
    }

    /**
     * Data for the ApexCharts line chart (products per category).
     *
     * @return array{categories: array<int, string>, series: array<int, int>}
     */
    public function getProductsPerCategoryChartProperty(): array
    {
        $rows = Category::query()
            ->withCount('products')
            ->whereHas('products')
            ->orderByDesc('products_count')
            ->limit(8)
            ->get(['name', 'products_count']);

        return [
            'categories' => $rows->pluck('name')->values()->all(),
            'series' => $rows->pluck('products_count')->map(fn ($n) => (int) $n)->values()->all(),
        ];
    }
}
