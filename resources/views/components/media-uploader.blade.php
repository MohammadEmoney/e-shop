@props([
    'model' => null,
    'collection' => 'images',
    'label' => 'آپلود رسانه',
    'accept' => 'image/*',
    'multiple' => false,
    'modelKey' => 'uploads',
    'showMainControl' => true,
    'tempUploads' => [],
])

<div
    x-data="{
        dragging: false,
        openDialog() { this.$refs.fileInput.click(); },
        dropped(e) {
            this.dragging = false;
            const dt = e.dataTransfer;
            if (!dt || !dt.files || dt.files.length === 0) return;
            const input = this.$refs.fileInput;
            input.files = dt.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },
    }"
    class="media-uploader"
>
    <label class="mb-2.5 block text-sm font-medium text-dark">{{ $label }}</label>

    {{-- Drop zone --}}
    <div
        @click="openDialog"
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="dropped"
        :class="dragging ? 'border-primary bg-gray-4' : 'border-form-stroke bg-white'"
        class="flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed px-6 py-8 text-center"
    >
        <p class="text-sm text-[#64748B]">
            فایل را اینجا رها کنید یا
            <span class="font-medium text-primary underline">انتخاب کنید</span>
        </p>
        <p class="mt-1 text-xs text-[#99A2B7]">{{ $multiple ? 'چندین فایل مجاز است' : 'حداکثر یک فایل' }}</p>

        <input
            type="file"
            wire:model="{{ $modelKey }}"
            @if ($multiple) multiple @endif
            accept="{{ $accept }}"
            class="hidden"
            x-ref="fileInput"
        >
    </div>

    {{-- Main image preview --}}
    @if ($model && $model->getFirstMedia('main_image'))
        <div class="mt-3 rounded-lg border border-stroke p-3">
            <p class="mb-2 text-sm font-medium text-dark">تصویر اصلی</p>
            <div class="flex items-center gap-3">
                <img src="{{ $model->getFirstMedia('main_image')->getUrl('webp') ?: $model->getFirstMedia('main_image')->getUrl() }}"
                     alt="main" class="h-24 w-24 rounded object-cover">
                <button type="button" wire:click="removeMainImage"
                        class="rounded-lg bg-danger px-3 py-1.5 text-sm text-white hover:opacity-90">
                    حذف تصویر اصلی
                </button>
            </div>
        </div>
    @endif

    {{-- Upload progress --}}
    <div x-data="{ progress: 0 }"
         x-on:livewire-upload-progress.window="if ($event.detail.name === '{{ $modelKey }}') { progress = $event.detail.progress; }"
         x-on:livewire-upload-error.window="progress = 0"
         class="mt-3"
         x-show="progress > 0 && progress < 100"
    >
        <div class="mb-1 flex items-center justify-between text-xs text-[#64748B]">
            <span>در حال آپلود…</span>
            <span x-text="progress + '%'"></span>
        </div>
        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-4">
            <div class="h-full rounded-full bg-primary transition-all" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>

    {{-- Temp uploads preview (files selected but not yet saved) --}}
    @if (count($tempUploads))
        <div class="mt-4">
            <p class="mb-2 text-sm font-medium text-dark">فایل‌های انتخاب‌شده</p>
            <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                @foreach ($tempUploads as $tempIndex => $tempFile)
                    <div class="group relative overflow-hidden rounded-lg border border-stroke">
                        <img src="{{ $tempFile->temporaryUrl() }}" alt="پیش‌نمایش"
                             class="h-24 w-full object-cover">
                        <button
                            type="button"
                            wire:click="removeTempUpload('{{ $modelKey }}', {{ $tempIndex }})"
                            class="absolute right-1 top-1 rounded bg-danger px-2 py-0.5 text-xs text-white opacity-0 transition group-hover:opacity-100"
                        >×</button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Existing media previews --}}
    @if ($model && $model->getMedia($collection)->count())
        <div class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-4">
            @foreach ($model->getMedia($collection) as $media)
                <div class="group relative overflow-hidden rounded-lg border border-stroke">
                    @if (str($media->mime_type)->startsWith('image/'))
                        <img src="{{ $media->getUrl('webp') ?: $media->getUrl() }}" alt="{{ $media->name }}" class="h-24 w-full object-cover">
                    @else
                        <div class="flex h-24 items-center justify-center bg-gray-4 text-[#64748B]">
                            <span class="text-xs">{{ str($media->mime_type)->after('/')->upper() }}</span>
                        </div>
                    @endif
                    <button
                        type="button"
                        wire:click="removeMedia({{ $media->id }})"
                        wire:confirm="این فایل حذف شود؟"
                        class="absolute right-1 top-1 rounded bg-danger px-2 py-0.5 text-xs text-white opacity-0 transition group-hover:opacity-100"
                    >×</button>
                    @if ($showMainControl)
                        <button
                            type="button"
                            wire:click="setMainImage({{ $media->id }})"
                            class="absolute bottom-1 right-1 rounded bg-primary px-2 py-0.5 text-xs text-white opacity-0 transition group-hover:opacity-100"
                        >اصلی</button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>