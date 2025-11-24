<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'eng_name' => 'BREAKFAST',
                'mm_name' => 'နံနက်စာ',
            ],
            [
                'eng_name' => 'APPETIZERS',
                'mm_name' => 'အစားစာများ',
            ],
            [
                'eng_name' => 'SALAD',
                'mm_name' => 'အသုပ်',
            ],
            [
                'eng_name' => 'ALACARTE',
                'mm_name' => ''
            ],
            [
                'eng_name' => 'EVENING MEALS',
                'mm_name' => 'ညနေစာများ'
            ],
            [
                'eng_name' => 'SOUP',
                'mm_name' => 'ဟင်းရည်'
            ],
        ];

        foreach ($categories as $key => $category) {
            # code...
            Category::create([
                'eng_name' => $category['eng_name'],
                'mm_name' => $category['mm_name']
            ]);
        }
    }
}
