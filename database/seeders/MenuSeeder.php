<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'category_id' => 1,
                'eng_name' => 'SHAN NOODLES',
                'mm_name' => 'ရှမ်းခေါက်ဆွဲ',
                'price' => 4000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'MEESHAY NOODLES',
                'mm_name' => 'မြီးရှည်',
                'price' => 4000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'NOODLE SLAD',
                'mm_name' => 'ခေါက်ဆွဲသုပ်',
                'price' => 4000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'MIXED VEGGIES NOODLE SALAD',
                'mm_name' => 'အစုံသုပ်',
                'price' => 4000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'NANGYI NOODLE SALAD',
                'mm_name' => 'နန်းကြီးသုပ်',
                'price' => 4000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'NANPYAR NOODLE SALAD',
                'mm_name' => 'နန်းပြားသုပ်',
                'price' => 4000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'BURMESE TEA',
                'mm_name' => 'မြန်မာလက်ဖက်ရည်',
                'price' => null,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'RANGOON STYLE FRIED RICE',
                'mm_name' => 'ရန်ကုန်ထမင်းကြော်',
                'price' => null,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'Fried Egg',
                'mm_name' => 'ကြက်ဥကြော်',
                'price' => 1000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'ROTI AND CHICKPEA',
                'mm_name' => 'ပဲပလာတာ',
                'price' => 2000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'ROTI AND CHICKEN',
                'mm_name' => 'ကီးမားပလာတာ',
                'price' => 5000,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ],
            [
                'category_id' => 1,
                'eng_name' => 'SAMUSA',
                'mm_name' => 'ဆမူဆာ',
                'price' => 700,
                'eng_description' => null,
                'mm_description' => null,
                'image_path' => null,
                'is_available' => true,
            ]
        ];


        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
