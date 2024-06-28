<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'url' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '会社名は必須です。',
            'description.required' => '説明は必須です。',
            'postal_code.required' => '郵便番号は必須です。',
            'address.required' => '住所は必須です。',
            'phone.required' => '電話番号は必須です。',
            'url.required' => 'URLは必須です。',
            'image.image' => '画像ファイルをアップロードしてください。',
            'image.mimes' => '有効な画像形式（jpeg, png, jpg, gif, svg）を選択してください。',
            'image.max' => '画像ファイルのサイズは2048KB以下にしてください。',
        ];
    }
}
