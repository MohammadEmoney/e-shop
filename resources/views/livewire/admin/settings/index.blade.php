<div class="flex flex-col gap-5">
    @if (session('status'))
        <div class="rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <div>
        <h2 class="text-xl font-bold text-dark">تنظیمات سایت</h2>
    </div>

    <form wire:submit="save" class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">نام سایت</label>
                <input type="text" wire:model="site_name"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('site_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">تلفن تماس</label>
                <input type="text" wire:model="contact_phone"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('contact_phone') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">ایمیل</label>
                <input type="email" wire:model="contact_email"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('contact_email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-5">
            <label class="mb-2.5 block text-sm font-medium text-dark">آدرس</label>
            <textarea wire:model="address" rows="3"
                      class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary"></textarea>
            @error('address') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>