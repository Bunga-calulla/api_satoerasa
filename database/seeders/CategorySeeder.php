<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Berat',  'icon' => '🍽️'],
            ['name' => 'Camilan',        'icon' => '🍿'],
            ['name' => 'Minuman',        'icon' => '🥤'],
            ['name' => 'Dessert',        'icon' => '🍰'],
            ['name' => 'Sarapan',        'icon' => '🍳'],
            ['name' => 'Masakan Nusantara', 'icon' => '🥘'],
            ['name' => 'Masakan Barat',  'icon' => '🍝'],
            ['name' => 'Masakan Asia',   'icon' => '🍜'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
