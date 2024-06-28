<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\TimeSlotHelper;
use App\Models\Shop;

class StoreReservationRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        $shop = $this->route('shop');
        return [
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required', 'date_format:H:i', Rule::in(TimeSlotHelper::getTimeSlots($shop))],
            'number_of_people' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'reservation_date.after_or_equal' => '過去の日時は選択できません。',
            'reservation_time.in' => '営業時間外の予約はできません。',
            'number_of_people.min' => '人数は1人以上で指定してください。',
        ];
    }
}
