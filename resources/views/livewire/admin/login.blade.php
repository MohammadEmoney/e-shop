<div class="w-full max-w-[440px] rounded-[10px] border border-stroke bg-white shadow-1 p-8">
    <h1 class="mb-1 text-title-md font-bold text-dark">ورود به پنل مدیریت</h1>
    <p class="mb-6 text-sm font-medium text-[#64748B]">با نام کاربری و رمز عبور وارد شوید</p>

    @if (session('status'))
        <div class="mb-4 rounded-lg border border-success-light bg-success-light/50 px-4 py-3 text-sm text-success">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="flex flex-col gap-5">
        <div>
            <label for="username" class="mb-2.5 block text-sm font-medium text-dark">نام کاربری</label>
            <input type="text"
                   wire:model="username"
                   id="username"
                   autocomplete="username"
                   class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm text-dark outline-none transition focus:border-primary"
                   placeholder="admin">
            @error('username')
                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="mb-2.5 block text-sm font-medium text-dark">رمز عبور</label>
            <input type="password"
                   wire:model="password"
                   id="password"
                   autocomplete="current-password"
                   class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm text-dark outline-none transition focus:border-primary"
                   placeholder="••••••••">
            @error('password')
                <p class="mt-1 text-sm text-danger">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full rounded-lg bg-primary px-5 py-3 text-sm font-medium text-white transition hover:bg-primary-dark">
            ورود
        </button>
    </form>
</div>