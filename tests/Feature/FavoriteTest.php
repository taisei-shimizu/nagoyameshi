<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shop;
use App\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate'); // テスト実行前にマイグレーションを実行
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']); // シーダーを実行
    }

    // お気に入り追加
    public function testUserCanAddFavorite()
    {
        $user = User::factory()->create(['member_type' => 'paid']);$user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.store', $shop));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);
    }

    // 無料会員はお気に入り追加不可
    public function testUserCanNotAddFavorite()
    {
        $user = User::factory()->create(['member_type' => 'free']);
        $shop = Shop::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.store', $shop));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]); // お気に入りが登録されていないことを確認

    }

    // お気に入り削除
    public function testUserCanRemoveFavorite()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $shop));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);
    }

    // 無料会員はお気に入り削除不可
    public function testUserCanNotRemoveFavorite()
    {
        $user = User::factory()->create(['member_type' => 'free']);
        $shop = Shop::factory()->create();

        Favorite::create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);

        $response = $this->actingAs($user)->delete(route('favorites.destroy', $shop));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]); // お気に入りが削除されていないことを確認
    }
}
