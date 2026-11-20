@php
    $admin = Auth::guard('admin')->user();
    $can = fn (string $permission): bool => (bool) ($admin?->hasPermissionTo($permission));
    $current = request()->routeIs('admin.dashboard');
@endphp

<aside class="hidden lg:flex flex-col w-[290px] bg-[#1C2434] shrink-0 overflow-y-auto">
    <div class="flex items-center gap-3 px-6 py-6">
        <a href="{{ route('admin.dashboard') }}" class="text-white font-bold text-xl">
            {{ config('app.name', 'مدیریت') }}
        </a>
    </div>

    <nav class="flex flex-col gap-1 px-4 pb-8">
        <p class="px-3 mb-3 text-[12px] font-semibold uppercase tracking-wider text-[#8A99AF]">منو</p>

        @php
            $items = [
                ['route' => 'admin.dashboard', 'label' => 'داشبورد', 'perm' => 'dashboard.view', 'pattern' => 'admin.dashboard'],
                ['route' => 'admin.products', 'label' => 'محصولات', 'perm' => 'products.view', 'pattern' => 'admin.products*'],
                ['route' => 'admin.categories', 'label' => 'دسته‌بندی‌ها', 'perm' => 'categories.view', 'pattern' => 'admin.categories*'],
                ['route' => 'admin.orders', 'label' => 'سفارش‌ها', 'perm' => 'orders.view', 'pattern' => 'admin.orders*'],
                ['route' => 'admin.admins', 'label' => 'مدیران', 'perm' => 'admins.view', 'pattern' => 'admin.admins*'],
                ['route' => 'admin.settings', 'label' => 'تنظیمات', 'perm' => 'settings.view', 'pattern' => 'admin.settings*'],
            ];
        @endphp

        @foreach ($items as $item)
            @if ($can($item['perm']))
                @php
                    $isActive = request()->routeIs($item['pattern']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-[15px] font-medium transition-colors {{ $isActive ? 'bg-primary text-white' : 'text-[#AEB7C9] hover:bg-[#333A48] hover:text-white' }}">
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </nav>
</aside>