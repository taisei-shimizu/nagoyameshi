<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShopRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'budget_lower' => 'required|integer',
            'budget_upper' => 'required|integer',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'closed_day' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '店舗名は必須です。',
            'discription.required' => '店舗の説明は必須です。',
            'category_id.required' => 'カテゴリーは必須です。',
            'category_id.exists' => '選択されたカテゴリーは有効ではありません。',
            'budget_lower.required' => '予算（下限）は必須です。',
            'budget_upper.required' => '予算（上限）は必須です。',
            'opening_time.required' => '営業開始時間は必須です。',
            'closing_time.required' => '営業終了時間は必須です。',
            'closed_day.required' => '定休日は必須です。',
            'postal_code.required' => '郵便番号は必須です。',
            'address.required' => '住所は必須です。',
            'phone.required' => '電話番号は必須です。',
            'image.required' => '画像は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.mimes' => '画像ファイルはjpeg,png,jpg,gif,svg形式を選択してください。',
            'image.max' => '画像ファイルは2MB以下にしてください。',
        ];
    }
}
