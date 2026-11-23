<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">{{ $blog ? 'ویرایش مقاله' : 'مقاله جدید' }}</h2>
        <a href="{{ route('admin.blogs') }}" class="text-sm font-medium text-primary">بازگشت</a>
    </div>

    <form wire:submit="save" class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
        {{-- Media uploader (images) at the top of the form --}}
        <div class="mb-6">
            <x-media-uploader
                :model="$blog"
                collection="images"
                label="تصویر شاخص و گالری"
                accept="image/jpeg,image/png,image/webp,image/gif"
                :multiple="true"
                modelKey="uploads"
                :tempUploads="$uploads ? (is_array($uploads) ? $uploads : [$uploads]) : []"
            />
        </div>

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">عنوان</label>
                <input type="text" wire:model="title"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('title') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">اسلاگ</label>
                <input type="text" wire:model="slug"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('slug') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-datepicker
                    :model="'published_at'"
                    label="تاریخ انتشار (شمسی)"
                    format="YYYY-MM-DD"
                />
                @error('published_at') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-end gap-3 pb-3">
                <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-dark">
                    <input type="checkbox" wire:model="is_published" class="h-4 w-4">
                    منتشر شده
                </label>
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-2.5 block text-sm font-medium text-dark">خلاصه</label>
            <textarea wire:model="excerpt" rows="2"
                      class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary"></textarea>
            @error('excerpt') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-5">
            <label class="mb-2.5 block text-sm font-medium text-dark">محتوا</label>
            <textarea wire:model="content" rows="8"
                      class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary"></textarea>
            @error('content') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>