<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // 過去12ヶ月間の会員登録数を取得
        $monthlyRegistrations = User::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
        ->groupBy('month')
        ->orderBy('month', 'asc')
        ->where('created_at', '>=', Carbon::now()->subYear())
        ->get()
        ->pluck('count', 'month')
        ->toArray();

        // 月のラベルと会員登録数を取得
        $months = [];
        $counts = [];

        for ($i = 0; $i < 12; $i++) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $months[] = Carbon::now()->subMonths($i)->format('Y年m月');
            $counts[] = $monthlyRegistrations[$month] ?? 0;
        }

        $months = array_reverse($months);
        $counts = array_reverse($counts);

        return view('admin.dashboard', compact('months', 'counts'));
    }
}
