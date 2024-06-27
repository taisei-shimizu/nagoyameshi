<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $companyData = [
        'name' => 'テスト会社',
        'description' => 'これはテスト会社です。',
        'postal_code' => '1234567',
        'address' => 'テスト住所',
        'phone' => '0123456789',
        'url' => 'https://testcompany.com',
        'image' => 'testimage.jpg',
    ];

    public function testShowCompany()
    {
        $company = Company::create($this->companyData);

        $response = $this->get(route('company'));

        $response->assertStatus(200);
        $response->assertViewHas('company', $company);
    }

    public function testEditCompany()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        $company = Company::create($this->companyData);

        $response = $this->get(route('admin.company.edit'));

        $response->assertStatus(200);
        $response->assertViewHas('company', $company);
    }

    public function testUpdateCompany()
    {
        // 管理者ユーザーを作成しログイン
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($admin);

        Storage::fake('public');
        $company = Company::create($this->companyData);

        $file = UploadedFile::fake()->image('company.jpg');

        $updatedData = [
            'name' => '更新された会社名',
            'description' => '更新された会社説明',
            'postal_code' => '7654321',
            'address' => '更新された住所',
            'phone' => '0987654321',
            'url' => 'https://updatedcompany.com',
            'image' => $file,
        ];

        $response = $this->patch(route('admin.company.update'), $updatedData);

        $response->assertRedirect(route('admin.company.edit'));
        $response->assertSessionHas('message', '会社情報を更新しました。');

        $this->assertDatabaseHas('companies', [
            'name' => '更新された会社名',
            'description' => '更新された会社説明',
            'postal_code' => '7654321',
            'address' => '更新された住所',
            'phone' => '0987654321',
            'url' => 'https://updatedcompany.com',
        ]);
    }
}
