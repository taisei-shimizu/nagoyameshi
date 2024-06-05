<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => '味噌カツ', 'description' => '濃厚な味噌だれをかけた豚カツ', 'slug' => 'miso-katsu'],
            ['name' => '手羽先', 'description' => '名古屋名物の手羽先唐揚げ', 'slug' => 'tebasaki'],
            ['name' => 'ひつまぶし', 'description' => '鰻の蒲焼を乗せたご飯', 'slug' => 'hitsumabushi'],
            ['name' => 'きしめん', 'description' => '幅広の平たい麺が特徴のうどん', 'slug' => 'kishimen'],
            ['name' => '台湾ラーメン', 'description' => '辛味の効いたラーメン', 'slug' => 'taiwan-ramen'],
            ['name' => 'あんかけスパゲッティ', 'description' => 'とろみのあるソースをかけたスパゲッティ', 'slug' => 'ankake-spaghetti'],
            ['name' => 'エビフライ', 'description' => '大きなエビを使ったフライ', 'slug' => 'ebi-fry'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'slug' => $category['slug'],
            ]);
        }
    }
}
