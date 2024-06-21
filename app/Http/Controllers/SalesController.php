<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Subscription;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $perPage = 10; // 1ページあたりの件数

        // サブスクリプションの最初の登録日を取得
        /*
        $firstChargeQuery = Charge::all(['limit' => 1]);
        $firstCharge = collect($firstChargeQuery->data)->sortBy('created')->first();
        $firstChargeDate = $firstCharge ? Carbon::createFromTimestamp($firstCharge->created)->toDateString() : Carbon::now()->toDateString();
        */

        // デフォルトの日付範囲を設定
        if (!$startDate) {
            $startDate = '2024-05-01'; // 最初の日
        }
        if (!$endDate) {
            $endDate = Carbon::now()->toDateString(); // 現在の日
        }
        dd($startDate,$endDate);

        // 全ての売上を取得（ページングなし）
        $charges = collect();
        $hasMore = true;
        $starting_after = null;

        while ($hasMore) {
            $query = [
                'limit' => 100,
                'starting_after' => $starting_after,
            ];
            $chargesQuery = Charge::all($query);
            $charges = $charges->merge($chargesQuery->data);
            $hasMore = $chargesQuery->has_more;
            if ($hasMore) {
                $starting_after = end($chargesQuery->data)->id;
            }
        }

        // 日付フィルタリング
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();
        $filteredCharges = $charges->filter(function ($charge) use ($start, $end) {
            $chargeDate = Carbon::createFromTimestamp($charge->created);
            return $chargeDate->between($start, $end);
        });

        // 顧客IDをユーザーIDと名前に変換
        $filteredCharges = $filteredCharges->map(function ($charge) {
            $user = User::where('stripe_id', $charge->customer)->first();
            return [
                'id' => $charge->id,
                'user_id' => $user ? $user->id : null,
                'user_name' => $user ? $user->name : 'Unknown',
                'amount' => $charge->amount,
                'created' => $charge->created,
            ];
        });

        // 合計売上と件数
        $totalSales = $filteredCharges->sum('amount');
        $totalCharges = $filteredCharges->count();

        // ページネーション
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginatedItems = $filteredCharges->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $chargesPaginated = new LengthAwarePaginator($paginatedItems, count($filteredCharges), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('admin.sales.index', compact('chargesPaginated', 'totalSales', 'totalCharges', 'startDate', 'endDate'));
    }
}
