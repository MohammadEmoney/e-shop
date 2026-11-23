@props([
    'label' => null,
    'model' => null,
    'format' => 'YYYY-MM-DD',
    'withTime' => false,
])

{{-- Reusable Persian (Jalali) datepicker bound to a Livewire property via
     Alpine $wire.entangle. Initialized inside x-init (runs once per mount). --}}
<div
    wire:ignore
    x-data="{ value: $wire.entangle('{{ $model }}') }"
    x-init="
        $nextTick(() => {
            const el = $el.querySelector('input');
            const fmt = '{{ $format }}';
            $(el).persianDatepicker({
                format: fmt,
                calendar: { persian: { locale: 'en', leapYearMode: 'astronomical' } },
                timePicker: {
                    enabled: {{ $withTime ? 'true' : 'false' }},
                    meridiem: { enabled: true },
                },
                onSelect: function (unix) {
                    value = new persianDate(unix).format(fmt);
                },
            });
            // Reflect the Livewire value into the picker.
            if (value) {
                $(el).val(value);
            }
        });
    "
>
    @if ($label)
        <label for="{{ $model }}" class="mb-2.5 block text-sm font-medium text-dark">{{ $label }}</label>
    @endif
    <input
        type="text"
        id="{{ $model }}"
        autocomplete="off"
        {{ $attributes }}
        class="datepickerInput w-full rounded-lg border border-form-stroke bg-white px-5 py-3 text-sm text-dark outline-none focus:border-primary"
    >
</div>