<?php

namespace Database\Seeders;

use App\Models\FnbCategory;
use App\Models\FnbMenu;
use Illuminate\Database\Seeder;

class FnbSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan Berat', 'icon' => '🍽️', 'sort_order' => 1],
            ['name' => 'Makanan Ringan', 'icon' => '🍟', 'sort_order' => 2],
            ['name' => 'Minuman', 'icon' => '🥤', 'sort_order' => 3],
            ['name' => 'Dessert', 'icon' => '🍰', 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            $category = FnbCategory::create($cat);

            if ($category->name === 'Makanan Berat') {
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Nasi Goreng Spesial', 'price' => 35000, 'description' => 'Nasi goreng dengan telur, ayam, dan sayuran']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Mie Goreng', 'price' => 30000, 'description' => 'Mie goreng dengan topping pilihan']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Ayam Bakar', 'price' => 45000, 'description' => 'Ayam bakar dengan sambal dan lalapan']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Soto Ayam', 'price' => 28000, 'description' => 'Soto ayam kuah bening dengan pelengkap']);
            }

            if ($category->name === 'Makanan Ringan') {
                FnbMenu::create(['category_id' => $category->id, 'name' => 'French Fries', 'price' => 22000, 'description' => 'Kentang goreng renyah']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Chicken Wings', 'price' => 38000, 'description' => '6 pcs sayap ayam goreng crispy']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Spring Rolls', 'price' => 25000, 'description' => 'Lumpia goreng isi sayuran']);
            }

            if ($category->name === 'Minuman') {
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Jus Jeruk Segar', 'price' => 18000, 'description' => 'Jus jeruk peras langsung']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Es Teh Manis', 'price' => 10000, 'description' => 'Teh manis dingin segar']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Kopi Arabica', 'price' => 25000, 'description' => 'Kopi arabica pilihan, panas/dingin']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Mineral Water', 'price' => 8000, 'description' => 'Air mineral botol 600ml']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Smoothie Mangga', 'price' => 22000, 'description' => 'Smoothie mangga segar dengan yogurt']);
            }

            if ($category->name === 'Dessert') {
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Pisang Goreng Keju', 'price' => 20000, 'description' => 'Pisang goreng dengan topping keju dan cokelat']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Es Krim Vanilla', 'price' => 18000, 'description' => '2 scoop es krim vanilla premium']);
                FnbMenu::create(['category_id' => $category->id, 'name' => 'Pudding Cokelat', 'price' => 15000, 'description' => 'Pudding cokelat lembut dengan saus karamel']);
            }
        }
    }
}
