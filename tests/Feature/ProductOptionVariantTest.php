<?php

use App\Livewire\Admin\Attributes\Form as AttributeForm;
use App\Livewire\Admin\Categories\Form as CategoryForm;
use App\Livewire\Admin\Options\Form as OptionForm;
use App\Livewire\Admin\Products\Form as ProductForm;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed([
        AdminSeeder::class,
        RolesAndPermissionsSeeder::class,
    ]);
});

function admin(): Admin
{
    return Admin::query()->where('username', 'admin')->firstOrFail();
}

it('gates the attribute and option admin pages behind their permissions', function () {
    $this->actingAs(admin(), 'admin')
        ->get(route('admin.attributes'))
        ->assertOk();
    $this->actingAs(admin(), 'admin')
        ->get(route('admin.options'))
        ->assertOk();

    $viewer = Admin::query()->create([
        'username' => 'viewer-'.uniqid(),
        'name' => 'Viewer',
        'password' => 'password',
    ]);
    $viewer->syncRoles(['viewer']);

    $this->actingAs($viewer, 'admin')
        ->get(route('admin.attributes'))
        ->assertOk(); // viewer has attributes.view
    $this->actingAs($viewer, 'admin')
        ->get(route('admin.attributes.create'))
        ->assertForbidden(); // viewer lacks attributes.create
    $this->actingAs($viewer, 'admin')
        ->get(route('admin.options.create'))
        ->assertForbidden();
});

it('creates an attribute with its values via the attribute form', function () {
    Livewire::actingAs(admin(), 'admin')
        ->test(AttributeForm::class)
        ->set('name', 'جنس')
        ->set('slug', 'material')
        ->set('values', [
            ['id' => null, 'value' => 'پنبه'],
            ['id' => null, 'value' => 'کتان'],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $attribute = ProductAttribute::query()->where('slug', 'material')->first();

    expect($attribute)->not->toBeNull()
        ->and($attribute->values()->pluck('value')->all())->toBe(['پنبه', 'کتان']);
});

it('creates an option with its values via the option form', function () {
    Livewire::actingAs(admin(), 'admin')
        ->test(OptionForm::class)
        ->set('name', 'سایز')
        ->set('slug', 'size')
        ->set('values', [
            ['id' => null, 'value' => 'کوچک (S)', 'slug' => 's'],
            ['id' => null, 'value' => 'بزرگ (L)', 'slug' => 'l'],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $option = ProductOption::query()->where('slug', 'size')->first();

    expect($option)->not->toBeNull()
        ->and($option->values()->pluck('slug')->all())->toBe(['s', 'l']);
});

it('attaches options and attributes to a category via the category form', function () {
    $size = ProductOption::factory()->create();
    $color = ProductOption::factory()->create();
    $material = ProductAttribute::factory()->create();

    Livewire::actingAs(admin(), 'admin')
        ->test(CategoryForm::class)
        ->set('name', 'لباس کودک')
        ->set('slug', 'kids-clothing')
        ->set('selectedOptions', [$size->id, $color->id])
        ->set('selectedAttributes', [$material->id])
        ->call('save')
        ->assertHasNoErrors();

    $category = Category::query()->where('slug', 'kids-clothing')->first();

    expect($category)->not->toBeNull()
        ->and($category->options()->pluck('product_options.id')->sort()->values()->all())->toBe([$size->id, $color->id])
        ->and($category->attributes()->pluck('product_attributes.id')->all())->toBe([$material->id]);
});

it('generates and saves option-based variants with unique slugs on the product form', function () {
    $category = Category::factory()->create();

    // size option with S, M, L
    $size = ProductOption::factory()->create(['name' => 'سایز', 'slug' => 'size']);
    $s = ProductOptionValue::factory()->create(['product_option_id' => $size->id, 'value' => 'کوچک', 'slug' => 's']);
    $m = ProductOptionValue::factory()->create(['product_option_id' => $size->id, 'value' => 'متوسط', 'slug' => 'm']);
    $l = ProductOptionValue::factory()->create(['product_option_id' => $size->id, 'value' => 'بزرگ', 'slug' => 'l']);

    // color option with blue, white
    $color = ProductOption::factory()->create(['name' => 'رنگ', 'slug' => 'color']);
    $blue = ProductOptionValue::factory()->create(['product_option_id' => $color->id, 'value' => 'آبی', 'slug' => 'blue']);
    $white = ProductOptionValue::factory()->create(['product_option_id' => $color->id, 'value' => 'سفید', 'slug' => 'white']);

    // material attribute (spec)
    $material = ProductAttribute::factory()->create(['name' => 'جنس', 'slug' => 'material']);
    $cotton = ProductAttributeValue::factory()->create(['product_attribute_id' => $material->id, 'value' => 'پنبه']);

    $category->options()->attach([$size->id, $color->id]);
    $category->attributes()->attach([$material->id]);

    Livewire::actingAs(admin(), 'admin')
        ->test(ProductForm::class)
        ->set('name', 'پیراهن دخترانه')
        ->set('slug', 'girls-shirt')
        ->set('category_id', $category->id)
        ->set('description', 'توضیحات محصول')
        ->set('price', 100000)
        ->set('stock', 1)
        ->set('is_active', true)
        ->set('selectedOptions', [
            $size->id => [$s->id, $m->id, $l->id],
            $color->id => [$blue->id, $white->id],
        ])
        ->set('selectedAttributeValues', [$cotton->id])
        ->call('generateVariants')
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::query()->where('slug', 'girls-shirt')->first();

    expect($product)->not->toBeNull()
        ->and($product->variants()->count())->toBe(6)
        ->and($product->attributeValues()->pluck('product_attribute_values.id')->all())->toBe([$cotton->id]);

    // Every variant carries the main price and default stock.
    expect($product->variants()->pluck('price')->all())->each->toBe(100000);

    // Variant slugs combine the option slugs with the product slug (unique).
    // Order: size first (selected first), then color.
    $slugs = $product->variants()->pluck('slug')->sort()->values()->all();
    $expected = [
        'l-blue-girls-shirt',
        'l-white-girls-shirt',
        'm-blue-girls-shirt',
        'm-white-girls-shirt',
        's-blue-girls-shirt',
        's-white-girls-shirt',
    ];

    expect($slugs)->toBe($expected)
        ->and(count(array_unique($slugs)))->toBe(6);

    // The variant -> optionValue pivot is populated (one value per dimension).
    $variant = $product->variants()->where('slug', 'm-blue-girls-shirt')->first();
    expect($variant)->not->toBeNull();

    $optionValueIds = $variant->optionValues()->pluck('product_option_values.id')->all();

    expect(count($optionValueIds))->toBe(2)
        ->and($variant->optionValues()->pluck('product_option_id')->sort()->values()->all())
        ->toBe([$size->id, $color->id]);
});
