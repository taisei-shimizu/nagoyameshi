<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    // カテゴリーデータをシーダーで登録
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--class' => 'CategorySeeder']);
    }

    /**
     * ユーザー用店舗一覧の表示テスト
     *
     * @return void
     */
    public function testUserCanViewShopIndex()
    {
        $user = User::factory()->create();

        $category = Category::first();
        Shop::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get(route('shops.index'));

        $response->assertStatus(200);
        $response->assertViewHas('shops');
        $response->assertViewHas('categories');
    }

    /**
     * 管理者用店舗一覧の表示テスト
     *
     * @return void
     */
    public function testAdminCanViewShopIndex()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::first();
        Shop::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->get(route('admin.shops.index'));

        $response->assertStatus(200);
        $response->assertViewHas('shops');
        $response->assertViewHas('total');
        $response->assertViewHas('categories');
    }

    /**
     * 管理者用店舗作成ページの表示テスト
     *
     * @return void
     */
    public function testAdminCanViewShopCreate()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($admin)->get(route('admin.shops.create'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    /**
     * 店舗の新規登録テスト
     *
     * @return void
     */
    public function testAdminCanStoreShop()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('images');

        $category = Category::first();

        $response = $this->actingAs($admin)->post(route('admin.shops.store'), [
            'name' => 'TestShop',
            'description' => 'TestDescription',
            'category_id' => $category->id,
            'budget_lower' => 1000,
            'budget_upper' => 5000,
            'opening_time' => '09:00',
            'closing_time' => '21:00',
            'closed_day' => '日曜日',
            'postal_code' => '123-4567',
            'address' => 'TestAddress',
            'phone' => '090-1234-5678',
            'image' => UploadedFile::fake()->image('shop.jpg'),
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.shops.index'));
        $this->assertDatabaseHas('shops', ['name' => 'TestShop']);
    }

    /**
     * 管理者用店舗編集ページの表示テスト
     *
     * @return void
     */
    public function testAdminCanViewShopEdit()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::first();
        $shop = Shop::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->get(route('admin.shops.edit', $shop->id));

        $response->assertStatus(200);
        $response->assertViewHas('shop');
        $response->assertViewHas('categories');
    }

    /**
     * 店舗情報の更新テスト
     *
     * @return void
     */
    public function testAdminCanUpdateShop()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Storage::fake('images');

        $category = Category::first();
        $shop = Shop::factory()->create(['category_id' => $category->id]);

        $updateData = [
            'name' => 'UpdatedShop',
            'description' => 'UpdatedDescription',
            'category_id' => $category->id,
            'budget_lower' => 1500,
            'budget_upper' => 6000,
            'opening_time' => '10:00',
            'closing_time' => '19:00',
            'closed_day' => '月曜日',
            'postal_code' => '765-4321',
            'address' => 'UpdatedAddress',
            'phone' => '080-8765-4321',
            'image' => UploadedFile::fake()->image('updated_shop.jpg')
        ];

        $response = $this->actingAs($admin)->put(route('admin.shops.update', $shop->id), $updateData);

        $response->assertRedirect(route('admin.shops.index'));
        $response->assertSessionHas('message', '店舗情報を更新しました。');
        $this->assertDatabaseHas('shops', ['name' => 'UpdatedShop']);
    }

    /**
     * 店舗情報の削除テスト
     *
     * @return void
     */
    public function testAdminCanDeleteShop()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::first();
        $shop = Shop::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->delete(route('admin.shops.destroy', $shop->id));

        $response->assertRedirect(route('admin.shops.index'));
        $response->assertSessionHas('message', '店舗を削除しました。');
        $this->assertSoftDeleted('shops', ['id' => $shop->id]);
    }

    /**
     * 店舗情報のCSVエクスポートテスト
     *
     * @return void
     */
    public function testAdminCanExportShops()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::first();
        Shop::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->actingAs($admin)->get(route('admin.shops.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="shops.csv"');
    }
    

    /**
     * 店舗情報のCSVインポートテスト
     *
     * @return void
     */
    public function testAdminCanImportShops()
    {
        {
            $admin = User::factory()->create(['role' => 'admin']);
            Storage::fake('local');

            // テンプレートをダウンロード
            $response = $this->actingAs($admin)->get(route('admin.shops.template'));
            $response->assertStatus(200);

            // ダウンロードしたテンプレートを保存
            $templateContent = $response->getContent();
            Storage::disk('local')->put('template.csv', $templateContent);

            // テンプレートを使ってインポートテスト
            $response = $this->actingAs($admin)->post(route('admin.shops.import'), [
                'csv_file' => UploadedFile::fake()->createWithContent('template.csv', $templateContent)
            ]);

            $response->assertRedirect(route('admin.shops.index'));
            $response->assertSessionHas('message', 'CSVファイルからのインポートが完了しました。');

            // データベースに新しいショップが登録されていることを確認
            $this->assertDatabaseHas('shops', ['name' => 'サンプル店舗1']);
            $this->assertDatabaseHas('shops', ['name' => 'サンプル店舗2']);
        }
    }
}