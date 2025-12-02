<?php

namespace Database\Seeders;

use App\Models\MenuVariant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuVariantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menu_variants = [
            // မြီးရှည်
            [
                'menu_id' => 2,
                'name' => 'chicken',
                'price' => 4000,
                'is_available' => true,
            ],
            [
                'menu_id' => 2,
                'name' => 'pork',
                'price' => 5000,
                'is_available' => true,
            ],

            // ရှမ်းခေါက်ဆွဲ
            [
                'menu_id' => 1,
                'name' => 'chicken',
                'price' => 4000,
                'is_available' => true,
            ],
            [
                'menu_id' => 1,
                'name' => 'pork',
                'price' => 5000,
                'is_available' => true,
            ],

            // မြန်မာ့လက်ဖက်ရည်
            [
                'menu_id' => 7,
                'name' => 'ရိုးရိုး',
                'price' => 2000,
                'is_available' => true,
            ],
            [
                'menu_id' => 7,
                'name' => 'တိုင်ကီ',
                'price' => 3000,
                'is_available' => true,
            ],
        ];

        foreach ($menu_variants as $menu_variant) {
            MenuVariant::create($menu_variant);
        }
    }
}
