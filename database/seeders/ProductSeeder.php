<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed products with integer Toman prices.
     *
     * @return array<int, array{
     *     category: string,
     *     name: string,
     *     price: int,
     *     compare_at_price: int|null,
     *     stock: int,
     *     short_description: string,
     *     description: string,
     *     variants?: array<int, array{name: string, price: int, stock: int, attributes: array<string, string>}>
     * }>
     */
    private function products(): array
    {
        return [
            [
                'category' => 'پیراهن مردانه',
                'name' => 'پیراهن مردانه آکسفورد',
                'price' => 850000,
                'compare_at_price' => 950000,
                'stock' => 40,
                'short_description' => 'پیراهن آکسفورد با پارچه تنفسی و دوخت عالی',
                'description' => 'پیراهن مردانه آکسفورد با الیاف پنبه‌ای، مناسب استفاده روزمره و اداری. دارای یقه کلاسیک و مچ‌های قابل تنظیم.',
                'variants' => [
                    ['name' => 'سفید / M', 'price' => 850000, 'stock' => 12, 'attributes' => ['color' => 'سفید', 'size' => 'M']],
                    ['name' => 'سفید / L', 'price' => 850000, 'stock' => 10, 'attributes' => ['color' => 'سفید', 'size' => 'L']],
                    ['name' => 'آبی / M', 'price' => 850000, 'stock' => 8, 'attributes' => ['color' => 'آبی', 'size' => 'M']],
                ],
            ],
            [
                'category' => 'پیراهن مردانه',
                'name' => 'پیراهن مردانه جین',
                'price' => 720000,
                'compare_at_price' => null,
                'stock' => 25,
                'short_description' => 'پیراهن جین مقاوم و شیک برای استایل کژوال',
                'description' => 'پیراهن جین با پارچه محکم و بافت کلاسیک، مناسب استایل روزمره و نیمه رسمی. شست‌وشوی آسان و ماندگاری بالا.',
            ],
            [
                'category' => 'شلوار مردانه',
                'name' => 'شلوار کتان مردانه',
                'price' => 680000,
                'compare_at_price' => 760000,
                'stock' => 55,
                'short_description' => 'شلوار کتان سبک و راحت',
                'description' => 'شلوار کتان با برش راسته، تنفس پذیری بالا و مناسب هر فصل. دارای جیب‌های کاربردی و دوخت مقاوم.',
                'variants' => [
                    ['name' => 'سرمه‌ای / 32', 'price' => 680000, 'stock' => 15, 'attributes' => ['color' => 'سرمه‌ای', 'size' => '32']],
                    ['name' => 'مشکی / 34', 'price' => 680000, 'stock' => 12, 'attributes' => ['color' => 'مشکی', 'size' => '34']],
                ],
            ],
            [
                'category' => 'کفش مردانه',
                'name' => 'کفش اسپرت مردانه',
                'price' => 1250000,
                'compare_at_price' => null,
                'stock' => 30,
                'short_description' => 'کفش اسپرت با رویه مش و زیره ضدلغزش',
                'description' => 'کفش اسپرت مردانه با رویه مش تنفس‌پذیر، زیره سبک ضدلغزش و مناسب پیاده‌روی و استفاده روزمره.',
            ],
            [
                'category' => 'مانتو',
                'name' => 'مانتوی زنانه اداری',
                'price' => 980000,
                'compare_at_price' => 1120000,
                'stock' => 20,
                'short_description' => 'مانتوی اداری با پارچه مرکوس و فرم ساده',
                'description' => 'مانتوی زنانه با پارچه مرکوس، برش راسته و طراحی مینیمال مناسب محیط اداری. دارای دکمه مخفی و جیب جانبی.',
                'variants' => [
                    ['name' => 'مشکی / M', 'price' => 980000, 'stock' => 7, 'attributes' => ['color' => 'مشکی', 'size' => 'M']],
                    ['name' => 'طوسی / L', 'price' => 980000, 'stock' => 5, 'attributes' => ['color' => 'طوسی', 'size' => 'L']],
                ],
            ],
            [
                'category' => 'شال و روسری',
                'name' => 'شال و روسری حریر',
                'price' => 350000,
                'compare_at_price' => null,
                'stock' => 80,
                'short_description' => 'شال حریر با طرح شاد و رنگ‌های متنوع',
                'description' => 'شال و روسری حریر با لطافت بالا، طرح‌های متنوع و مناسب ست کردن با استایل‌های مختلف.',
            ],
            [
                'category' => 'کیف زنانه',
                'name' => 'کیف دوشی زنانه',
                'price' => 890000,
                'compare_at_price' => 990000,
                'stock' => 22,
                'short_description' => 'کیف دوشی با بند قابل تنظیم و ابعاد مناسب',
                'description' => 'کیف دوشی زنانه با جنس چرم مصنوعی، بند بلند قابل تنظیم و چند جیب داخلی. مناسب استفاده روزمره.',
            ],
            [
                'category' => 'موبایل',
                'name' => 'گوشی موبایل هوشمند ۲۵۶ گیگ',
                'price' => 18500000,
                'compare_at_price' => null,
                'stock' => 15,
                'short_description' => 'گوشی هوشمند با حافظه ۲۵۶ گیگ و دوربین سه‌گانه',
                'description' => 'گوشی هوشمند با صفحه‌نمایش امولد، پردازنده قدرتمند، دوربین سه‌گانه و باتری با دوام بالا. دارای حافظه ۲۵۶ گیگابایت.',
                'variants' => [
                    ['name' => 'مشکی / ۲۵۶ گیگ', 'price' => 18500000, 'stock' => 7, 'attributes' => ['color' => 'مشکی', 'storage' => '256GB']],
                    ['name' => 'آبی / ۵۱۲ گیگ', 'price' => 21000000, 'stock' => 5, 'attributes' => ['color' => 'آبی', 'storage' => '512GB']],
                ],
            ],
            [
                'category' => 'لپ تاپ',
                'name' => 'لپ تاپ ۱۶ گیگ رم',
                'price' => 34500000,
                'compare_at_price' => 37000000,
                'stock' => 8,
                'short_description' => 'لپ تاپ سبک با رم ۱۶ گیگ و حافظه SSD',
                'description' => 'لپ تاپ با پردازنده نسل جدید، ۱۶ گیگابایت رم، حافظه SSD سریع و صفحه‌نمایش با کیفیت. مناسب کار و برنامه‌نویسی.',
            ],
            [
                'category' => 'هدفون',
                'name' => 'هدفون بلوتوثی بی‌سیم',
                'price' => 2450000,
                'compare_at_price' => null,
                'stock' => 45,
                'short_description' => 'هدفون بی‌سیم با نویزکنسلینگ و باتری طولانی',
                'description' => 'هدفون بلوتوثی با تکنولوژی حذف نویز، کیفیت صدای بالا، میکروفون داخلی و باتری با ماندگاری طولانی.',
            ],
            [
                'category' => 'لوازم خانگی',
                'name' => 'جاروبرقی رباتیک',
                'price' => 8900000,
                'compare_at_price' => 9800000,
                'stock' => 12,
                'short_description' => 'جاروبرقی رباتیک با ناوبری هوشمند و کنترل اپلیکیشن',
                'description' => 'جاروبرقی رباتیک با سنسورهای هوشمند، قابلیت کنترل از راه دور با اپلیکیشن و مخزن قابل شست‌وشو.',
            ],
            [
                'category' => 'ظروف پذیرایی',
                'name' => 'ست ظروف غذاخوری ۱۲ نفره',
                'price' => 4600000,
                'compare_at_price' => null,
                'stock' => 18,
                'short_description' => 'ست کامل ظروف غذاخوری سرامیکی ۱۲ نفره',
                'description' => 'ست ظروف غذاخوری سرامیکی ۱۲ نفره شامل بشقاب، کاسه و ظروف سرو. مقاوم در برابر حرارت و قابل استفاده در مایکروویو.',
            ],
            [
                'category' => 'مراقبت پوست',
                'name' => 'کرم مرطوب‌کننده پوست',
                'price' => 420000,
                'compare_at_price' => 480000,
                'stock' => 100,
                'short_description' => 'کرم مرطوب‌کننده با هیالورونیک اسید',
                'description' => 'کرم مرطوب‌کننده صورت حاوی هیالورونیک اسید و ویتامین E. مناسب پوست‌های خشک و نرمال. ماندگاری و جذب بالا.',
            ],
            [
                'category' => 'آرایشی',
                'name' => 'رژ لب مات',
                'price' => 280000,
                'compare_at_price' => null,
                'stock' => 60,
                'short_description' => 'رژ لب مات با ماندگاری طولانی',
                'description' => 'رژ لب مات با رنگدانه بالا، بافت سبک و ماندگاری طولانی. دارای رطوبت‌رسان و بدون ایجاد حس چسبندگی.',
            ],
        ];
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->products() as $index => $data) {
            $category = Category::query()->where('name', $data['category'])->firstOrFail();

            /** @var Product $product */
            $product = Product::query()->firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'category_id' => $category->id,
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'compare_at_price' => $data['compare_at_price'],
                    'stock' => $data['stock'],
                    'short_description' => $data['short_description'],
                    'description' => $data['description'],
                    'sku' => 'SKU-'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'is_active' => true,
                    'is_featured' => $index % 3 === 0,
                ]
            );

            // Populate denormalized AI-ready search_text from name + descriptions + category.
            $product->forceFill([
                'search_text' => implode(' ', array_filter([
                    $product->name,
                    $product->short_description,
                    $product->description,
                    $product->category?->name,
                ])),
            ])->save();

            foreach ($data['variants'] ?? [] as $variant) {
                ProductVariant::query()->firstOrCreate(
                    ['product_id' => $product->id, 'name' => $variant['name']],
                    [
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                        'attributes' => $variant['attributes'],
                        'sku' => $product->sku.'-'.strtoupper(Str::slug($variant['name'], '_')),
                    ]
                );
            }
        }
    }
}
