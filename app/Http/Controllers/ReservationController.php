<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Shop;
use App\Helpers\TimeSlotHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ReservationController extends Controller
{
    public function store(Request $request, Shop $shop)
    {
        $request->validate([
            'reservation_date' => ['required', 'date', 'after_or_equal:today'],
            'reservation_time' => ['required', 'date_format:H:i', Rule::in(TimeSlotHelper::getTimeSlots($shop))], 
            'number_of_people' => ['required', 'integer', 'min:1'],
        ],
        [
            'reservation_date.after_or_equal' => '過去の日時は選択できません。',
            'reservation_time.in' => '営業時間外の予約はできません。',
            'number_of_people.min' => '人数は1人以上で指定してください。'
        ]);

        // 予約日時が過去の日時かどうかをチェック
        $reservationDateTime = Carbon::parse($request->reservation_date . ' ' . $request->reservation_time, 'Asia/Tokyo');

        if ($reservationDateTime->lessThan(Carbon::now('Asia/Tokyo'))) {
            return redirect()->back()->withErrors(['reservation_date' => '過去の日時は選択できません。'])->withInput();
        }

        // 人数が1人以上かどうかをチェック
        if ($request->number_of_people <= 0) {
            return redirect()->back()->with('error', '1人以上で予約してください。')->withInput();
        }

        // 営業時間内かどうかをチェック
        $opening_time = Carbon::parse($shop->opening_time, 'Asia/Tokyo');
        $closing_time = Carbon::parse($shop->closing_time, 'Asia/Tokyo');
        $reservation_time = Carbon::parse($request->reservation_time, 'Asia/Tokyo');

        // 日付をまたぐ場合の営業時間チェック
        if ($closing_time < $opening_time) {
            if (!($reservation_time >= $opening_time || $reservation_time <= $closing_time)) {
                return redirect()->back()->withErrors(['reservation_time' => '営業時間外の予約はできません。']);
            }
        } else {
            if ($reservation_time < $opening_time || $reservation_time > $closing_time) {
                return redirect()->back()->withErrors(['reservation_time' => '営業時間外の予約はできません。']);
            }
        }

        // すでに同じ時間に予約があるかどうかをチェック
        $existing_reservation = Reservation::where('shop_id', $shop->id)
            ->where('reservation_date', $request->reservation_date)
            ->where('reservation_time', $request->reservation_time)
            ->exists();

        if ($existing_reservation) {
            return redirect()->back()->with('error', 'すでに予約が入っています。別の日時を選択してください。');
        }

        Reservation::create([
            'reservation_date' => $request->reservation_date,
            'reservation_time' => $request->reservation_time,
            'number_of_people' => $request->number_of_people,
            'shop_id' => $shop->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('shops.show', $shop)->with('message', '予約が完了しました。');
    }

    public function destroy(Reservation $reservation)
    {
        if (Auth::id() !== $reservation->user_id) {
            return redirect()->back()->with('error', '削除する権限がありません。');
        }

        $reservation->delete();

        return redirect()->back()->with('message', '予約をキャンセルしました。');
    }
}

