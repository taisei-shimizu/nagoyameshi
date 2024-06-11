<?php

namespace App\Helpers;

use App\Models\Shop;
use Carbon\Carbon;

class TimeSlotHelper
{
    public static function getTimeSlots(Shop $shop)
    {
        $times = [];
        $start = Carbon::parse($shop->opening_time, 'Asia/Tokyo');
        $end = Carbon::parse($shop->closing_time, 'Asia/Tokyo');

        if ($end->lessThan($start)) {
            // 日付をまたぐ場合
            $midnight = Carbon::parse('23:59', 'Asia/Tokyo');
            while ($start->lessThanOrEqualTo($midnight)) {
                $times[] = $start->format('H:i');
                $start->addMinutes(30);
            }
            $start = Carbon::parse('00:00', 'Asia/Tokyo');
        }

        while ($start->lessThanOrEqualTo($end)) {
            $times[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return $times;
    }
}

?>