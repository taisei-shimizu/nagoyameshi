<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // カテゴリIDと対応する画像ファイル
        $categoryImages = [
            '味噌カツ' => 'miso-katsu.jpg',
            '手羽先' => 'tebasaki.jpg',
            'ひつまぶし' => 'hitsumabushi.jpg',
            'きしめん' => 'kishimen.jpg',
            '台湾ラーメン' => 'taiwan-ramen.jpg',
            'あんかけスパゲッティ' => 'ankake-spaghetti.jpg',
            'エビフライ' => 'ebi-fry.jpg'
        ];

        // 有効なカテゴリIDをランダムに取得
        $category = Category::whereNull('deleted_at')->inRandomOrder()->first();
        $categoryId = $category->id;
        $categoryName = $category->name;

        return [
            'name' => $this->faker->company,
            'description' => $this->faker->realText(50,5),
            'image' => 'images/' . $categoryImages[$categoryName],
            'category_id' => $categoryId,
            'budget_lower' => $this->faker->numberBetween(500, 1000),
            'budget_upper' => $this->faker->numberBetween(2000, 5000),
            'opening_time' => $this->faker->time('H:i:s'),
            'closing_time' => $this->faker->time('H:i:s'),
            'closed_day' => $this->faker->dayOfWeek,
            'postal_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
