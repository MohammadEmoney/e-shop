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

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        {{-- Pie: orders by status --}}
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="mb-4 text-lg font-semibold text-dark">توزیع وضعیت سفارش‌ها</h3>
            <div id="orderStatusChart"></div>
        </div>

        {{-- Line/bar: products per category --}}
        <div class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
            <h3 class="mb-4 text-lg font-semibold text-dark">محصولات هر دسته</h3>
            <div id="categoryChart"></div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            initDashboardCharts();
        });
        Livewire.hook('morph.added', function () { initDashboardCharts(); });
        Livewire.hook('morph.updated', function () { initDashboardCharts(); });

        function initDashboardCharts() {
            if (!window.ApexCharts) return;

            const orderData = @json($this->orderStatusChart);
            const catData = @json($this->productsPerCategoryChart);

            if (document.getElementById('orderStatusChart') && !document.getElementById('orderStatusChart').apexcharts) {
                new ApexCharts(document.getElementById('orderStatusChart'), {
                    chart: { type: 'pie', fontFamily: 'inherit' },
                    labels: orderData.labels,
                    series: orderData.series,
                    legend: { position: 'bottom' },
                }).render();
            }

            if (document.getElementById('categoryChart') && !document.getElementById('categoryChart').apexcharts) {
                new ApexCharts(document.getElementById('categoryChart'), {
                    chart: { type: 'bar', fontFamily: 'inherit' },
                    series: [{ name: 'محصولات', data: catData.series }],
                    xaxis: { categories: catData.categories },
                    colors: ['#3056d3'],
                    plotOptions: { bar: { horizontal: false, columnWidth: '55%' } },
                }).render();
            }
        }
    </script>
@endpush