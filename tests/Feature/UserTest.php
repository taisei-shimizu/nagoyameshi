<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function testEditUser()
    {
        $user = User::factory()->create();

        // 編集フォーム表示を確認
        $response = $this->actingAs($user)->get(route('mypage.edit'));

        $response->assertStatus(200);
        $response->assertViewHas('user', $user);
    }

    public function testUpdateUser()
    {
        $user = User::factory()->create();
        // ユーザー情報の更新
        $updatedData = [
            'name' => 'テストユーザー',
            'email' => 'updated@example.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ];

        $response = $this->actingAs($user)->put(route('mypage.update'), $updatedData);

        $response->assertRedirect(route('mypage.edit'));
        $response->assertSessionHas('message', 'ユーザー情報が更新されました。');

        // データベースの確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'テストユーザー',
            'email' => 'updated@example.com',
        ]);
    }

    public function testShowMypage()
    {
        $user = User::factory()->create();

        // マイページ表示を確認
        $response = $this->actingAs($user)->get(route('mypage'));

        $response->assertStatus(200);
        $response->assertViewHas('user', $user);
    }

    public function testShowFavorites()
    {
        $user = User::factory()->create();

        // お気に入り一覧表示を確認
        $response = $this->actingAs($user)->get(route('mypage.favorites'));

        $response->assertStatus(200);
        $response->assertViewHas('favorites');
    }

    public function testShowReservations()
    {
        $user = User::factory()->create();

        // 予約一覧表示を確認
        $response = $this->actingAs($user)->get(route('mypage.reservations'));

        $response->assertStatus(200);
        $response->assertViewHas('pastReservations');
        $response->assertViewHas('futureReservations');
    }

    public function testDestroyUser()
    {
        $user = User::factory()->create();

        // 退会処理
        $response = $this->actingAs($user)->delete(route('users.destroy'));

        $response->assertRedirect('/');
        $response->assertSessionHas('message', '退会しました。');

        // データベースの確認（論理削除されていること）

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);
    }
}
