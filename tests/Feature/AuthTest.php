<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザー登録テスト
     *
     * @return void
     */
    public function testUserCanRegister()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/verify-email');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'member',
            'member_type' => 'free',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    /**
     * ユーザーログインテスト
     *
     * @return void
     */
    public function testUserCanLogin()
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'member_type' => 'free',
            'email_verified_at' => now(), // メール確認済み
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // $response = $this
        // ->actingAs($user)
        // ->get('/');
        // $response->assertOk();

        $response->assertStatus(302);
        $response->assertRedirect('/shops');
        $this->assertAuthenticatedAs($user);
    }

    /**
     * ユーザーログアウトテスト
     *
     * @return void
     */
    public function testUserCanLogout()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'member',
            'member_type' => 'free',
        ]);

        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertStatus(302);
        $response->assertRedirect('/'); 
        $this->assertGuest();
    }
}
