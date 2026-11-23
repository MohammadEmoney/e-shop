@props([
    'label' => null,
    'model' => null,
    'placeholder' => 'انتخاب کنید',
    'name' => null,
    'allowClear' => false,
    'required' => false,
])

{{-- Reusable Select2 dropdown bound to a Livewire property via Alpine $wire.entangle.
     Select2 is initialized inside Alpine x-init (runs once per element mount) which
     avoids the destroy/re-init conflict that causes the Select2 console error. --}}
<div
    wire:ignore
    x-data="{ value: $wire.entangle('{{ $model }}') }"
    x-init="
        $nextTick(() => {
            const el = $el.querySelector('select');
            const allowClear = {{ $allowClear ? 'true' : 'false' }};
            $(el).select2({
                placeholder: '{{ $placeholder }}',
                allowClear: allowClear,
                width: '100%',
            });
            // Reflect the Livewire value into Select2.
            $(el).val(value).trigger('change');
            // Sync Select2 changes back into the Livewire property.
            $(el).on('select2:select', (e) => { value = e.params.data.id; });
            $(el).on('select2:clear', () => { value = null; });
            // Keep Select2 in sync if Livewire changes the value from elsewhere.
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => {
                    if (String($(el).val()) !== String(value)) {
                        $(el).val(value).trigger('change');
                    }
                });
            });
        });
    "
>
    @if ($label)
        <label for="{{ $model }}" class="mb-2.5 block text-sm font-medium text-dark">{{ $label }}</label>
    @endif
    <select
        id="{{ $model }}"
        name="{{ $name ?? $model }}"
        {{ $attributes }}
        class="w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm text-dark outline-none focus:border-primary"
    >
        @if ($placeholder)
            <option value=""></option>
        @endif
        {{ $slot }}
    </select>
</div>