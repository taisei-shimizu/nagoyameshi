<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shop;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shops = [
            [
                'name' => '名古屋味噌カツ店',
                'description' => '最高の味噌カツを提供します。',
                'image' => 'images/miso-katsu.jpg',
                'category_id' => 1,
                'budget_lower' => 1000,
                'budget_upper' => 3000,
                'opening_time' => '11:00:00',
                'closing_time' => '22:00:00',
                'closed_day' => '水曜日',
                'postal_code' => '460-0001',
                'address' => '名古屋市中区1-1-1',
                'phone' => '052-123-4567'
            ],
            [
                'name' => '手羽先名店',
                'description' => '名古屋名物手羽先が楽しめます。',
                'image' => 'images/tebasaki.jpg',
                'category_id' => 2,
                'budget_lower' => 1500,
                'budget_upper' => 4000,
                'opening_time' => '12:00:00',
                'closing_time' => '23:00:00',
                'closed_day' => '木曜日',
                'postal_code' => '460-0002',
                'address' => '名古屋市中区2-2-2',
                'phone' => '052-234-5678'
            ],
        ];

        foreach ($shops as $shop) {
            Shop::create($shop);
        }

        Shop::factory()->count(50)->create();
    }
}
