@php
    $admin = Auth::guard('admin')->user();
@endphp

<header class="flex items-center justify-between border-b border-stroke bg-white px-4 py-4 md:px-6">
    <div>
        <h2 class="text-xl font-semibold text-dark">
            {{ $title ?? 'پنل مدیریت' }}
        </h2>
    </div>

    <div class="flex items-center gap-4">
        <div class="text-right">
            <p class="text-[15px] font-medium text-dark">{{ $admin?->name ?? $admin?->username }}</p>
            <p class="text-sm text-[#64748B]">{{ $admin?->username }}</p>
        </div>

        <livewire:admin.logout />
    </div>
</header>