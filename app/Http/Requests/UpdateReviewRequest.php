<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
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
            'score' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'score.required' => 'スコアは必須です。',
            'score.integer' => 'スコアは整数でなければなりません。',
            'score.between' => 'スコアは1から5の間でなければなりません。',
            'content.required' => 'レビュー内容は必須です。',
            'content.max' => 'レビュー内容は255文字以内でなければなりません。',
        ];
    }
}
