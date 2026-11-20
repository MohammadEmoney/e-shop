<div class="flex flex-col gap-6">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="text-sm font-medium text-[#64748B]">محصولات</h3>
            <p class="mt-2 text-2xl font-bold text-dark">{{ $this->stats['products'] }}</p>
        </div>
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="text-sm font-medium text-[#64748B]">دسته‌بندی‌ها</h3>
            <p class="mt-2 text-2xl font-bold text-dark">{{ $this->stats['categories'] }}</p>
        </div>
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="text-sm font-medium text-[#64748B]">سفارش‌ها</h3>
            <p class="mt-2 text-2xl font-bold text-dark">{{ $this->stats['orders'] }}</p>
        </div>
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="text-sm font-medium text-[#64748B]">مشتریان</h3>
            <p class="mt-2 text-2xl font-bold text-dark">{{ $this->stats['customers'] }}</p>
        </div>
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="text-sm font-medium text-[#64748B]">مدیران</h3>
            <p class="mt-2 text-2xl font-bold text-dark">{{ $this->stats['admins'] }}</p>
        </div>
    </div>
</div>