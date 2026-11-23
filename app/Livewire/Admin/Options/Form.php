<?php

namespace App\Livewire\Admin\Options;

use App\Livewire\Concerns\AuthorizesAdmin;
use App\Livewire\Concerns\DispatchesToasts;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('آپشن')]
class Form extends Component
{
    use AuthorizesAdmin;
    use DispatchesToasts;

    public ?ProductOption $option = null;

    public string $name = '';

    public string $slug = '';

    public string $type = ProductOption::TYPE_SELECT;

    public bool $is_active = true;

    public int $sort_order = 0;

    /**
     * Draft values for the option. Each item is a row {id, value, slug}.
     *
     * @var array<int, array{id: int|null, value: string, slug: string|null}>
     */
    public array $values = [];

    public function mount(?ProductOption $option = null): void
    {
        $this->authorizeAdmin($option ? 'options.edit' : 'options.create');

        $this->option = $option;

        if ($option) {
            $this->name = $option->name;
            $this->slug = $option->slug;
            $this->type = $option->type;
            $this->is_active = $option->is_active;
            $this->sort_order = $option->sort_order;

            $this->values = $option->values()
                ->orderBy('sort_order')
                ->get()
                ->map(fn (ProductOptionValue $v) => [
                    'id' => $v->id,
                    'value' => $v->value,
                    'slug' => $v->slug,
                ])
                ->all();
        }
    }

    public function updatedName(): void
    {
        if (! $this->option) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function addValue(): void
    {
        $this->values[] = ['id' => null, 'value' => '', 'slug' => null];
    }

    public function removeValue(int $index): void
    {
        // Deletions are applied to the database when the form is saved.
        unset($this->values[$index]);
        $this->values = array_values($this->values);
    }

    public function save(): void
    {
        $this->authorizeAdmin($this->option ? 'options.edit' : 'options.create');

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:product_options,slug,'.($this->option->id ?? 'NULL')],
            'type' => ['required', 'in:'.implode(',', [ProductOption::TYPE_SELECT, ProductOption::TYPE_COLOR])],
            'is_active' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'values' => ['required', 'array', 'min:1'],
            'values.*.value' => ['required', 'string', 'max:255'],
        ]);

        if ($this->option) {
            $this->option->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
            $option = $this->option;
        } else {
            $option = ProductOption::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'is_active' => $data['is_active'],
                'sort_order' => $data['sort_order'],
            ]);
        }

        // Sync values: create new, update existing, delete removed.
        $kept = [];
        foreach ($this->values as $order => $row) {
            $slug = $row['slug'] ?: Str::slug($row['value']);
            $payload = ['value' => $row['value'], 'slug' => $slug, 'sort_order' => $order];

            if (! empty($row['id'])) {
                $model = $option->values()->find($row['id']);
                if ($model) {
                    $model->update($payload);
                    $kept[] = $model->id;

                    continue;
                }
            }

            $model = $option->values()->create($payload);
            $kept[] = $model->id;
        }

        $option->values()->whereNotIn('id', $kept)->delete();

        $this->flashToast('آپشن ذخیره شد.');

        $this->redirectRoute('admin.options', navigate: true);
    }

    public function render(): string
    {
        return 'livewire.admin.options.form';
    }
}
