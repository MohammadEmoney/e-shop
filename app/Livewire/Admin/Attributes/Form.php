<?php

namespace App\Livewire\Admin\Attributes;

use App\Livewire\Concerns\AuthorizesAdmin;
use App\Livewire\Concerns\DispatchesToasts;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('ویژگی')]
class Form extends Component
{
    use AuthorizesAdmin;
    use DispatchesToasts;

    public ?ProductAttribute $attribute = null;

    public string $name = '';

    public string $slug = '';

    public string $type = ProductAttribute::TYPE_SELECT;

    public bool $is_filterable = true;

    public int $sort_order = 0;

    /**
     * Draft values for the attribute. Each item is a row {id, value}.
     *
     * @var array<int, array{id: int|null, value: string}>
     */
    public array $values = [];

    public function mount(?ProductAttribute $attribute = null): void
    {
        $this->authorizeAdmin($attribute ? 'attributes.edit' : 'attributes.create');

        $this->attribute = $attribute;

        if ($attribute) {
            $this->name = $attribute->name;
            $this->slug = $attribute->slug;
            $this->type = $attribute->type;
            $this->is_filterable = $attribute->is_filterable;
            $this->sort_order = $attribute->sort_order;

            $this->values = $attribute->values()
                ->orderBy('sort_order')
                ->get()
                ->map(fn (ProductAttributeValue $v) => [
                    'id' => $v->id,
                    'value' => $v->value,
                ])
                ->all();
        }
    }

    public function updatedName(): void
    {
        if (! $this->attribute) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function addValue(): void
    {
        $this->values[] = ['id' => null, 'value' => ''];
    }

    public function removeValue(int $index): void
    {
        // Deletions are applied to the database when the form is saved.
        unset($this->values[$index]);
        $this->values = array_values($this->values);
    }

    public function save(): void
    {
        $this->authorizeAdmin($this->attribute ? 'attributes.edit' : 'attributes.create');

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:product_attributes,slug,'.($this->attribute->id ?? 'NULL')],
            'type' => ['required', 'in:'.implode(',', [ProductAttribute::TYPE_SELECT, ProductAttribute::TYPE_COLOR, ProductAttribute::TYPE_TEXT])],
            'is_filterable' => ['boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'values' => ['required', 'array', 'min:1'],
            'values.*.value' => ['required', 'string', 'max:255'],
        ]);

        $data['values'] = array_values($this->values);

        if ($this->attribute) {
            $this->attribute->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'is_filterable' => $data['is_filterable'],
                'sort_order' => $data['sort_order'],
            ]);
            $attribute = $this->attribute;
        } else {
            $attribute = ProductAttribute::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'type' => $data['type'],
                'is_filterable' => $data['is_filterable'],
                'sort_order' => $data['sort_order'],
            ]);
        }

        // Sync values: create new, update existing, delete removed.
        $kept = [];
        foreach ($data['values'] as $order => $row) {
            $payload = ['value' => $row['value'], 'sort_order' => $order];

            if (! empty($row['id'])) {
                $model = $attribute->values()->find($row['id']);
                if ($model) {
                    $model->update($payload);
                    $kept[] = $model->id;

                    continue;
                }
            }

            $model = $attribute->values()->create($payload);
            $kept[] = $model->id;
        }

        $attribute->values()->whereNotIn('id', $kept)->delete();

        $this->flashToast('ویژگی ذخیره شد.');

        $this->redirectRoute('admin.attributes', navigate: true);
    }

    public function render(): string
    {
        return 'livewire.admin.attributes.form';
    }
}
