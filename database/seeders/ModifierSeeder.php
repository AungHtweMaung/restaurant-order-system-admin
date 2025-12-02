<?php

namespace Database\Seeders;

use App\Models\Modifier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModifierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modifiers = [
            // flavor
            [
                'name' => 'ချိူဆိမ့်',
                'type' => 'flavor'
            ],
            [
                'name' => 'ပုံမှန်',
                'type' => 'flavor'
            ],
            [
                'name' => 'ပေါ့ဆိမ့်',
                'type' => 'flavor'
            ],
            [
                'name' => 'ကျဆိမ့်',
                'type' => 'flavor'
            ],


            // avoid
            [
                'name' => 'အငန်လျော့',
                'type' => 'avoid'
            ],
            [
                'name' => 'အစိမ်းရှောင်',
                'type' => 'avoid'
            ],
            [
                'name' => 'အချိူမှုန့်ရှောင်',
                'type' => 'avoid'
            ]
        ];

        foreach ($modifiers as $modifier) {
            Modifier::create($modifier);
        }
    }
}
