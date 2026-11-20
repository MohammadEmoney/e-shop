<?php

use App\Livewire\Admin\Admins\Form as AdminForm;
use App\Livewire\Admin\Admins\Index as AdminIndex;
use App\Livewire\Admin\Categories\Form as CategoryForm;
use App\Livewire\Admin\Categories\Index as CategoryIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Admin\Products\Form as ProductForm;
use App\Livewire\Admin\Products\Index as ProductIndex;
use App\Livewire\Admin\Settings\Index as SettingIndex;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
|
| These routes are authenticated against the dedicated admin guard
| (username + password). Access requires the admin.auth middleware, and
| individual permissions are enforced via permission:* on each route.
|
| NOTE: The spatie `permission` middleware receives the guard explicitly
| (`permission:xxx,admin`) so authorization NEVER falls back to the default
| (web) guard.
|
*/

// Public login route (guest:admin).
Route::get('/admin/login', Login::class)
    ->middleware('guest:admin')
    ->name('admin.login');

// Authenticated admin area.
Route::prefix('admin')
    ->name('admin.')
    ->middleware('admin.auth')
    ->group(function (): void {

        Route::get('/', Dashboard::class)
            ->middleware('permission:dashboard.view,admin')
            ->name('dashboard');

        // Products
        Route::get('products', ProductIndex::class)
            ->middleware('permission:products.view,admin')
            ->name('products');
        Route::get('products/create', ProductForm::class)
            ->middleware('permission:products.create,admin')
            ->name('products.create');
        Route::get('products/{product}/edit', ProductForm::class)
            ->middleware('permission:products.edit,admin')
            ->name('products.edit');

        // Categories
        Route::get('categories', CategoryIndex::class)
            ->middleware('permission:categories.view,admin')
            ->name('categories');
        Route::get('categories/create', CategoryForm::class)
            ->middleware('permission:categories.create,admin')
            ->name('categories.create');
        Route::get('categories/{category}/edit', CategoryForm::class)
            ->middleware('permission:categories.edit,admin')
            ->name('categories.edit');

        // Orders
        Route::get('orders', OrderIndex::class)
            ->middleware('permission:orders.view,admin')
            ->name('orders');

        // Admins (super-admin area)
        Route::get('admins', AdminIndex::class)
            ->middleware('permission:admins.view,admin')
            ->name('admins');
        Route::get('admins/create', AdminForm::class)
            ->middleware('permission:admins.create,admin')
            ->name('admins.create');
        Route::get('admins/{admin}/edit', AdminForm::class)
            ->middleware('permission:admins.update,admin')
            ->name('admins.edit');

        // Settings
        Route::get('settings', SettingIndex::class)
            ->middleware('permission:settings.view,admin')
            ->name('settings');
    });
