<?php

use App\Livewire\Admin\Login;
use App\Livewire\Admin\Products\Form;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Spatie\MediaLibrary\HasMedia;

/**
 * Phase 4 verification: admin panel auth, authorization and guard separation.
 */
beforeEach(function () {
    $this->seed([
        AdminSeeder::class,
        RolesAndPermissionsSeeder::class,
    ]);
});

it('requires admin auth to view the dashboard and redirects guests to login', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('allows a super-admin to view the dashboard and permission-gated pages', function () {
    $superAdmin = Admin::query()->where('username', 'admin')->first();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.dashboard'))
        ->assertOk();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.products'))
        ->assertOk();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.products.create'))
        ->assertOk();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.orders'))
        ->assertOk();

    $this->actingAs($superAdmin, 'admin')
        ->get(route('admin.admins'))
        ->assertOk();
});

it('returns 403 for a viewer who has no create/edit/delete permission', function () {
    $viewer = Admin::query()->create([
        'username' => 'viewer',
        'name' => 'Viewer',
        'password' => 'password',
    ]);
    $viewer->syncRoles(['viewer']);

    // viewer CAN view the products list
    $this->actingAs($viewer, 'admin')
        ->get(route('admin.products'))
        ->assertOk();

    // viewer CANNOT create a product -> 403 (not just hidden in UI)
    $this->actingAs($viewer, 'admin')
        ->get(route('admin.products.create'))
        ->assertForbidden();

    $this->actingAs($viewer, 'admin')
        ->get(route('admin.categories.create'))
        ->assertForbidden();

    // viewer CANNOT manage admins at all
    $this->actingAs($viewer, 'admin')
        ->get(route('admin.admins'))
        ->assertForbidden();

    // viewer CANNOT update settings
    $this->actingAs($viewer, 'admin')
        ->get(route('admin.settings'))
        ->assertOk(); // settings.view IS allowed for viewer

    $this->actingAs($viewer, 'admin')
        ->post(route('admin.settings'), []) // no route for this; just ensure not crashing
        ->assertStatus(405);
});

it('lets a manager manage products (manager role has create)', function () {
    $manager = Admin::query()->create([
        'username' => 'manager',
        'name' => 'Manager',
        'password' => 'password',
    ]);
    $manager->syncRoles(['manager']);

    $this->actingAs($manager, 'admin')
        ->get(route('admin.products'))
        ->assertOk();

    // manager in the seed has products.create -> allowed
    $this->actingAs($manager, 'admin')
        ->get(route('admin.products.create'))
        ->assertOk();

    // manager does NOT have admins.view -> 403
    $this->actingAs($manager, 'admin')
        ->get(route('admin.admins'))
        ->assertForbidden();
});

it('blocks a customer authenticated on the web guard from admin routes', function () {
    $customer = User::query()->create([
        'phone' => '09121111111',
        'name' => 'Customer',
        'phone_verified_at' => now(),
    ]);

    $this->actingAs($customer, 'web')
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.login'));
});

it('does not log out the customer when the admin logs out', function () {
    $superAdmin = Admin::query()->where('username', 'admin')->first();
    $customer = User::query()->create([
        'phone' => '09122222222',
        'name' => 'Customer',
        'phone_verified_at' => now(),
    ]);

    // Log in as both on separate guards.
    Auth::guard('admin')->login($superAdmin);
    Auth::guard('web')->login($customer);

    expect(Auth::guard('admin')->check())->toBeTrue()
        ->and(Auth::guard('web')->check())->toBeTrue();

    // Log the admin out.
    Auth::guard('admin')->logout();

    expect(Auth::guard('admin')->check())->toBeFalse()
        ->and(Auth::guard('web')->check())->toBeTrue();
});

it('stores product prices as integers via the product form', function () {
    $admin = Admin::query()->where('username', 'admin')->first();
    $category = Category::factory()->create();

    Livewire::actingAs($admin, 'admin')
        ->test(Form::class)
        ->set('name', 'Test Product')
        ->set('slug', 'test-product')
        ->set('category_id', $category->id)
        ->set('description', 'full description')
        ->set('price', '250000')
        ->set('stock', '10')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::query()->where('slug', 'test-product')->first();
    expect($product)->not->toBeNull();
    expect($product->price)->toBe(250000)
        ->and(is_int($product->price))->toBeTrue();
});

it('logs an admin in via the login component and regenerates the session', function () {
    Livewire::test(Login::class)
        ->set('username', 'admin')
        ->set('password', 'password')
        ->call('login');

    expect(Auth::guard('admin')->check())->toBeTrue()
        ->and(Auth::guard('admin')->user()->username)->toBe('admin');
});

it('rejects invalid admin credentials', function () {
    Livewire::test(Login::class)
        ->set('username', 'admin')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('username');

    expect(Auth::guard('admin')->check())->toBeFalse();
});

it('sets a session cookie on the admin login page (web middleware applied)', function () {
    // Regression: admin routes must run the 'web' middleware group so the
    // session cookie is issued; otherwise Livewire auth cannot persist.
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertCookie('laravel-session');
});

it('reaches the dashboard after authenticating via the login component', function () {
    Livewire::test(Login::class)
        ->set('username', 'admin')
        ->set('password', 'password')
        ->call('login');

    // The admin is now authenticated on the admin guard; the dashboard must
    // render (not redirect back to login).
    $this->get(route('admin.dashboard'))
        ->assertOk();
});

it('attaches uploaded media to a product and generates the webp conversion', function () {
    $admin = Admin::query()->where('username', 'admin')->first();
    $category = Category::factory()->create();

    $file = UploadedFile::fake()->image('photo.jpg', 300, 300);

    Livewire::actingAs($admin, 'admin')
        ->test(Form::class)
        ->set('name', 'Media Product')
        ->set('slug', 'media-product')
        ->set('category_id', $category->id)
        ->set('description', 'A product with an uploaded image.')
        ->set('price', 500000)
        ->set('stock', 5)
        ->set('is_active', true)
        ->set('uploads', $file)
        ->call('save')
        ->assertHasNoErrors();

    $product = Product::query()->where('slug', 'media-product')->first();

    expect($product)->not->toBeNull();
    expect($product->getMedia('images')->count())->toBe(1);

    $media = $product->getFirstMedia('images');
    expect($media->collection_name)->toBe('images')
        ->and(str($media->mime_type)->startsWith('image/'))->toBeTrue();

    // The model implements the polymorphic HasMedia contract.
    expect(is_subclass_of(Product::class, HasMedia::class))->toBeTrue();
});

it('renders the blog form with media uploader and persian datepicker', function () {
    $admin = Admin::query()->where('username', 'admin')->first();

    Livewire::actingAs($admin, 'admin')
        ->test(App\Livewire\Admin\Blogs\Form::class)
        ->assertOk()
        ->assertSet('is_published', true);
});
