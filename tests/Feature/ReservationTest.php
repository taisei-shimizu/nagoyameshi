<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']);
    }

    public function testUserCanCreateReservation()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00'
        ]);

        $reservationDate = Carbon::now()->addDay()->format('Y-m-d');
        $reservationTime = '20:00'; // 有効時間内

        $response = $this->actingAs($user)->post(route('reservations.store', $shop), [
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'number_of_people' => 2,
        ]);

        $response->assertRedirect(route('shops.show', $shop));
        $response->assertSessionHas('message', '予約が完了しました。');

        $this->assertDatabaseHas('reservations', [
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'number_of_people' => 2,
        ]);
    }

    public function testUserCannotCreateReservationWithInvalidData()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00'
        ]);

        // 無効なデータで予約を作成しようとする
        $response = $this->actingAs($user)->post(route('reservations.store', $shop), [
            'reservation_date' => 'invalid-date',
            'reservation_time' => 'invalid-time',
            'number_of_people' => -1,
        ]);

        $response->assertSessionHasErrors(['reservation_date', 'reservation_time', 'number_of_people']);
    }

    public function testUserCannotCreateReservationForPastDate()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00'
        ]);

        $pastDate = Carbon::now()->subDay()->format('Y-m-d');

        $response = $this->actingAs($user)->post(route('reservations.store', $shop), [
            'reservation_date' => $pastDate,
            'reservation_time' => '20:00',
            'number_of_people' => 2,
        ]);

        $response->assertSessionHasErrors([
            'reservation_date' => '過去の日時は選択できません。',
        ]);
    }

    public function testUserCannotCreateReservationOutsideBusinessHours()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00',
        ]);

        $futureDate = Carbon::now()->addDay()->format('Y-m-d');

        // 営業時間前
        $responseBefore = $this->actingAs($user)->post(route('reservations.store', $shop), [
            'reservation_date' => $futureDate,
            'reservation_time' => '18:00',
            'number_of_people' => 2,
        ]);

        $responseBefore->assertSessionHasErrors([
            'reservation_time' => '営業時間外の予約はできません。'
        ]);

        // 営業時間後
        $responseAfter = $this->actingAs($user)->post(route('reservations.store', $shop), [
            'reservation_date' => $futureDate,
            'reservation_time' => '23:00',
            'number_of_people' => 2,
        ]);

        $responseAfter->assertSessionHasErrors([
            'reservation_time' => '営業時間外の予約はできません。'
        ]);
    }

    public function testUserCannotCreateReservationIfAlreadyExists()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00'
        ]);

        $reservationDate = Carbon::now()->addDay()->format('Y-m-d');
        $reservationTime = '20:00';

        Reservation::factory()->create([
            'shop_id' => $shop->id,
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
        ]);

        $response = $this->actingAs($user)->post(route('reservations.store', $shop), [
            'reservation_date' => $reservationDate,
            'reservation_time' => $reservationTime,
            'number_of_people' => 2,
        ]);

        $response->assertSessionHas('error', 'すでに予約が入っています。別の日時を選択してください。');
    }

    public function testUserCanDeleteReservation()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00'
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $response->assertRedirect();
        $response->assertSessionHas('message', '予約をキャンセルしました。');

        $this->assertDatabaseMissing('reservations', ['id' => $reservation->id]);
    }

    public function testUserCannotDeleteOtherUsersReservation()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $anotherUser = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create([
            'opening_time' => '19:00',
            'closing_time' => '22:00'
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'user_id' => $anotherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reservations.destroy', $reservation));

        $response->assertRedirect();
        $response->assertSessionHas('error', '削除する権限がありません。');

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
        ]);
    }
}
