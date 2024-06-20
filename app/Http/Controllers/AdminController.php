<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class AdminController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // 年間の売上を取得
        $currentYear = Carbon::now()->year;
        $sales = [];

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth()->timestamp;
            $endOfMonth = Carbon::create($currentYear, $month, 1)->endOfMonth()->timestamp;

            // サブスクリプションのチャージを取得
            $paymentIntents = PaymentIntent::all([
                'created' => [
                    'gte' => $startOfMonth,
                    'lte' => $endOfMonth,
                ],
                'limit' => 100,
            ]);

            $monthlySales = 0;
            foreach ($paymentIntents->data as $intent) {
                if ($intent->status == 'succeeded') {
                    $monthlySales += $intent->amount;
                }
            }
            $sales[] = $monthlySales;
        }

        // 過去12ヶ月間の会員登録数を取得
        $monthlyRegistrations = User::selectRaw('COUNT(*) as count, DATE_FORMAT(created_at, "%Y-%m") as month')
        ->where('role' , '!=', 'admin')
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

        return view('admin.dashboard', compact('sales', 'months', 'counts'));
    }
}
