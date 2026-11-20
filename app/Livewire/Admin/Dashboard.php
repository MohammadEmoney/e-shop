<?php

namespace App\Livewire\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('داشبورد مدیریت')]
class Dashboard extends Component
{
    public function render(): string
    {
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
}
