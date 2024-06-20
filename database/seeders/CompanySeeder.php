<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'name' => '株式会社Sodateru',
            'description' => '会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文会社の説明文',
            'image' => 'images/company.jpg',
            'postal_code' => '123-4567',
            'address' => '東京都千代田区1-1-1',
            'phone' => '03-1234-5678',
            'url' => 'https://example.com',
        ]);
    }
}
