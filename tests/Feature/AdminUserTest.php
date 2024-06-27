<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);
        // 管理者ユーザーを複数作成
        User::factory()->count(10)->create(['role' => 'admin']);

        $response = $this->get(route('admin.admins.index'));

        $response->assertStatus(200);
        $response->assertViewHas('admins');
    }

    public function testCreate()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $response = $this->get(route('admin.admins.create'));
        $response->assertStatus(200);
    }

    public function testStore()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $response = $this->post(route('admin.admins.store'), [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('message', '管理者が作成されました。');
        $this->assertDatabaseHas('users', [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);
    }

    public function testEdit()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->get(route('admin.admins.edit', $admin));

        $response->assertStatus(200);
        $response->assertViewHas('admin', $admin);
    }

    public function testUpdate()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->put(route('admin.admins.update', $admin), [
            'name' => 'Updated Admin',
            'email' => 'updated_admin@test.com',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('message', '管理者情報が更新されました。');
        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Updated Admin',
            'email' => 'updated_admin@test.com',
        ]);
    }

    public function testDestroy()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->delete(route('admin.admins.destroy', $admin));

        $response->assertRedirect(route('admin.admins.index'));
        $response->assertSessionHas('message', '管理者が削除されました。');
        $this->assertSoftDeleted('users', ['id' => $admin->id]);
    }
}