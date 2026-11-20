<div class="flex flex-col gap-5">
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-dark">{{ $admin ? 'ویرایش مدیر' : 'مدیر جدید' }}</h2>
        <a href="{{ route('admin.admins') }}" class="text-sm font-medium text-primary">بازگشت</a>
    </div>

    <form wire:submit="save" class="rounded-[10px] border border-stroke bg-white p-6 shadow-1">
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">نام کاربری</label>
                <input type="text" wire:model="username"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('username') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">نام</label>
                <input type="text" wire:model="name"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">
                    {{ $admin ? 'رمز عبور جدید (اختیاری)' : 'رمز عبور' }}
                </label>
                <input type="password" wire:model="password" autocomplete="new-password"
                       class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm outline-none focus:border-primary">
                @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="mb-2.5 block text-sm font-medium text-dark">نقش‌ها</label>
                <div class="flex flex-col gap-2">
                    @foreach (\Spatie\Permission\Models\Role::where('guard_name', 'admin')->get() as $role)
                        <label class="flex cursor-pointer items-center gap-2 text-sm font-medium text-dark">
                            <input type="checkbox" wire:model="roles" value="{{ $role->id }}" class="h-4 w-4">
                            {{ $role->name }}
                        </label>
                    @endforeach
                </div>
                @error('roles') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-sm font-medium text-white hover:bg-primary-dark">
                ذخیره
            </button>
        </div>
    </form>
</div>