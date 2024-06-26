<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // 現在の日付と時間を取得
        $now = Carbon::now();

        // 開店時間と閉店時間を設定
        $openingTime = Carbon::createFromTimeString('19:00');
        $closingTime = Carbon::createFromTimeString('22:00');

        // 開店時間から閉店時間の間のランダムな時間を設定
        $reservationTime = $this->faker->dateTimeBetween($openingTime, $closingTime)->format('H:i');

        return [
            'reservation_date' => $now->format('Y-m-d'),
            'reservation_time' => $reservationTime,
            'number_of_people' => $this->faker->numberBetween(1, 10),
            'shop_id' => Shop::factory(),
            'user_id' => User::factory(),
        ];
    }
}
