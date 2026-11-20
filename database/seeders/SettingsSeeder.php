<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Default site settings (Persian).
     *
     * @return array<int, array{key: string, value: string, group: string}>
     */
    private function settings(): array
    {
        return [
            ['key' => 'site_name', 'value' => 'فروشگاه آنلاین', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'Toman', 'group' => 'general'],
            ['key' => 'contact_phone', 'value' => '۰۲۱-۱۲۳۴۵۶۷۸', 'group' => 'contact'],
            ['key' => 'contact_email', 'value' => 'info@example.com', 'group' => 'contact'],
            ['key' => 'address', 'value' => 'تهران، خیابان آزادی، پلاک ۱', 'group' => 'contact'],
            ['key' => 'shipping_cost', 'value' => '45000', 'group' => 'shipping'],
            ['key' => 'free_shipping_threshold', 'value' => '2000000', 'group' => 'shipping'],
        ];
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->settings() as $setting) {
            Setting::query()->firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
