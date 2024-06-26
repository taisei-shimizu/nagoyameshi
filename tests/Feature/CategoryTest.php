<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    // 管理者ユーザーをシードで作成
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'UserSeeder']);
    }

    //カテゴリ一覧表示テスト
    public function testAdminCanViewCategories()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Category::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertViewHas('total');
    }

    //カテゴリ新規作成画面表示テスト
    public function testAdminCanCreateCategory()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $categoryData = [
            'name' => '新規カテゴリ',
            'description' => '説明文',
            'slug' => 'new-category'
        ];

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), $categoryData);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['name' => '新規カテゴリ']);
    }

    //カテゴリ新規作成テスト
    public function testAdminCanStoreCategory()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $categoryData = [
            'name' => '新規カテゴリ',
            'description' => '説明文',
            'slug' => 'new-category'
        ];

        $response = $this->actingAs($admin)->post(route('admin.categories.store'), $categoryData);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('message', 'カテゴリを作成しました。');
        $this->assertDatabaseHas('categories', ['name' => '新規カテゴリ']);
    }

    //カテゴリ編集画面表示テスト
    public function testAdminCanEditCategory()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
        $response->assertViewHas('category');
    }

    //カテゴリ更新テスト
    public function testAdminCanUpdateCategory()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $updateData = [
            'name' => '編集カテゴリ',
            'description' => '編集説明文',
            'slug' => 'updated-category'
        ];

        $response = $this->actingAs($admin)->put(route('admin.categories.update', $category->id), $updateData);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('message', 'カテゴリを更新しました。');
        $this->assertDatabaseHas('categories', ['name' => '編集カテゴリ']);
    }

    //カテゴリ削除テスト
    public function testAdminCanDeleteCategory()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.categories.destroy', $category->id));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('message', 'カテゴリを削除しました。');
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }
}
