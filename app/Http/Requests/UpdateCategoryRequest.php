<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'description' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $this->category->id,
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'カテゴリ名は必須です。',
            'description.required' => '説明は必須です。',
            'slug.required' => 'スラッグは必須です。',
            'slug.unique' => 'このスラッグはすでに使用されています。',
        ];
    }
}
