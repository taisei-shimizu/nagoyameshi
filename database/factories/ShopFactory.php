<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

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
            1 => 'miso-katsu.jpg',
            2 => 'tebasaki.jpg',
            3 => 'hitsumabushi.jpg',
            4 => 'kishimen.jpg',
            5 => 'taiwan-ramen.jpg',
            6 => 'ankake-spaghetti.jpg',
            7 => 'ebi-fry.jpg'
        ];

        $categoryId = $this->faker->numberBetween(1, 7);

        return [
            'name' => $this->faker->company,
            'description' => $this->faker->realText(50,5),
            'image' => 'images/' . $categoryImages[$categoryId],
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
