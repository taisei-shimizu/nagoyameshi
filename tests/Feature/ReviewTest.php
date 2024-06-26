<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']);
    }

    // レビュー投稿
    public function testUserCanCreateReview()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store', $shop), [
            'score' => 5,
            'content' => '美味しい!',
        ]);

        $response->assertRedirect(route('shops.show', $shop));
        $response->assertSessionHas('message', 'レビューを投稿しました。');

        $this->assertDatabaseHas('reviews', [
            'shop_id' => $shop->id,
            'user_id' => $user->id,
            'score' => 5,
            'content' => '美味しい!',
        ]);
    }

    // レビュー更新
    public function testUserCanUpdateReview()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create();
        $review = Review::factory()->create(['shop_id' => $shop->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->patch(route('reviews.update', $review), [
            'score' => 4,
            'content' => 'まあまあ',
        ]);

        $response->assertRedirect(route('shops.show', $shop));
        $response->assertSessionHas('message', 'レビューを更新しました。');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'score' => 4,
            'content' => 'まあまあ',
        ]);
    }

    // レビュー削除
    public function testUserCanDeleteReview()
    {
        $user = User::factory()->create(['member_type' => 'paid']);
        $shop = Shop::factory()->create();
        $review = Review::factory()->create(['shop_id' => $shop->id, 'user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertRedirect();
        $response->assertSessionHas('message', 'レビューを削除しました。');

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    // レビュー一覧表示
    public function testAdminCanViewReviewManagement()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::first();
        $shop = Shop::factory()->create(['category_id' => $category->id]);
        Review::factory()->count(5)->create(['shop_id' => $shop->id]);

        $response = $this->actingAs($admin)->get(route('admin.reviews.index'));

        $response->assertStatus(200);
        $response->assertViewHas('reviews');
        $response->assertViewHas('total');
        $response->assertViewHas('averageScore');
    }

    // レビュー公開状態変更
    public function testAdminCanToggleReviewPublish()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::first();
        $shop = Shop::factory()->create(['category_id' => $category->id]);
        $review = Review::factory()->create(['shop_id' => $shop->id, 'is_published' => false]);

        $response = $this->actingAs($admin)->patch(route('admin.reviews.togglePublish', $review->id));

        $response->assertRedirect(route('admin.reviews.index'));
        $response->assertSessionHas('message', 'レビューの公開状態を変更しました。');

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'is_published' => true,
        ]);
    }
}

