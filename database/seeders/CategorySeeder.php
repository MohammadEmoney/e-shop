<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Main categories and their subcategories (Persian).
     *
     * @return array<string, array<int, string>>
     */
    private function tree(): array
    {
        return [
            'لباس مردانه' => ['پیراهن مردانه', 'شلوار مردانه', 'کفش مردانه'],
            'لباس زنانه' => ['مانتو', 'شال و روسری', 'کیف زنانه'],
            'دیجیتال' => ['موبایل', 'لپ تاپ', 'هدفون'],
            'خانه و آشپزخانه' => ['لوازم خانگی', 'ظروف پذیرایی'],
            'زیبایی و بهداشت' => ['مراقبت پوست', 'آرایشی'],
        ];
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->tree() as $parentName => $children) {
            $parent = Category::query()->firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'description' => "دسته‌بندی {$parentName}",
                    'is_active' => true,
                    'sort_order' => 0,
                ]
            );

            foreach ($children as $index => $childName) {
                Category::query()->firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'name' => $childName,
                        'parent_id' => $parent->id,
                        'description' => "زیرمجموعه {$parentName}",
                        'is_active' => true,
                        'sort_order' => $index + 1,
                    ]
                );
            }
        }
    }
}
