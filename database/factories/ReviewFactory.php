<?php

namespace Database\Factories;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition()
    {
        return [
            'shop_id' => Shop::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'score' => $this->faker->numberBetween(1, 5),
            'content' => $this->faker->realText(50,5),
            'created_at' => $this->faker->dateTimeThisYear,
            'updated_at' => now(),
        ];
    }
}
