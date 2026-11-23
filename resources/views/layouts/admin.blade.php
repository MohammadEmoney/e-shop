<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? '' }} - {{ config('app.name', 'مدیریت') }}</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Local vendor libraries --}}
    <link href="/libs/select2/select2.min.css" rel="stylesheet">
    <link href="/libs/persiandate/persian-datepicker.css" rel="stylesheet">
    <link href="/libs/simplebar/simplebar.min.css" rel="stylesheet">
    <link href="/libs/toastr/toastr.min.css" rel="stylesheet">
    <link href="/libs/apexcharts/apexcharts.css" rel="stylesheet">
    <link href="/libs/fontawesome/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-[#F1F5F9]">

    <div class="flex h-screen overflow-hidden">
        {{-- Sidebar --}}
        @include('admin.partials.sidebar')

        {{-- Content area --}}
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden" data-simplebar>
            {{-- Topbar --}}
            @include('admin.partials.topbar')

            <main class="p-4 md:p-6 2xl:p-10">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- Local vendor scripts (order matters) --}}
    <script src="/libs/jquery/jquery.min.js"></script>
    <script src="/libs/select2/select2.min.js"></script>
    <script src="/libs/persiandate/persian-date.min.js"></script>
    <script src="/libs/persiandate/persian-datepicker.min.js"></script>
    <script src="/libs/simplebar/simplebar.min.js"></script>
    <script src="/libs/apexcharts/apexcharts.min.js"></script>
    <script src="/libs/toastr/toastr.min.js"></script>
    <script src="/libs/sweetalert2/sweetalert2.all.js"></script>

    @livewireScripts

    {{-- Global admin controls initializer --}}
    <script>
        window.initAdminControls = function () {
            // SimpleBar scrollbars
            document.querySelectorAll('[data-simplebar]').forEach(function (el) {
                if (!el.dataset.simplebarInited && window.SimpleBar) {
                    new SimpleBar(el);
                    el.dataset.simplebarInited = '1';
                }
            });

        };

        // Toast / alert helpers exposed globally.
        window.toast = function (message, type) {
            if (window.toastr) {
                toastr[type || 'success'](message || '', '', { closeButton: true, progressBar: true, rtl: true });
            }
        };
        // Listen for Livewire-dispatched toast events and show a toastr.
        document.addEventListener('toast', function (e) {
            window.toast(e.detail?.message, e.detail?.type || 'success');
        });
        window.confirmDelete = function (message, callback) {
            if (window.Swal) {
                Swal.fire({
                    title: message || 'آیا مطمئن هستید؟',
                    showCancelButton: true,
                    confirmButtonText: 'بله',
                    cancelButtonText: 'خیر',
                }).then(function (result) {
                    if (result.isConfirmed && callback) callback();
                });
            }
        };

        // Route Livewire's wire:confirm through SweetAlert for a nicer UX.
        document.addEventListener('livewire:init', function () {
            if (window.Livewire && !window.Livewire.confirmHasSwal) {
                window.Livewire.confirmHasSwal = true;
                window.Livewire.hook('request', function () {});
                document.addEventListener('confirm', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: e.detail.message || 'آیا مطمئن هستید؟',
                        showCancelButton: true,
                        confirmButtonText: 'بله',
                        cancelButtonText: 'خیر',
                    }).then(function (result) {
                        if (result.isConfirmed) e.detail.confirm(true);
                        else e.detail.confirm(false);
                    });
                });
            }
        });

        document.addEventListener('livewire:navigated', window.initAdminControls);
        // document.addEventListener('livewire:morph', window.initAdminControls);
        document.addEventListener('DOMContentLoaded', window.initAdminControls);
    </script>

    @stack('scripts')
</body>
</html>