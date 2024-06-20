<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('payment.index', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'paymentMethod' => 'required|string',
        ]);

        $user = Auth::user();
        $paymentMethod = $request->paymentMethod;

        // 固定の住所情報を設定
        $address = [
            'line1' => '愛知県',
            'city' => '名古屋市',
            'postal_code' => '457-0071',
            'country' => 'JP', // 日本をデフォルト値として設定
        ];

        try {
                DB::beginTransaction();
                // Stripeの顧客情報を作成または取得
                $user->createOrGetStripeCustomer();
                $user->updateDefaultPaymentMethod($paymentMethod);
                $user->updateStripeCustomer([
                    'address' => $address,
                ]);

                // サブスクリプションを作成
                $user->newSubscription('default', 'price_1PSWzZAAw8E2Bg3MEAkPVNX0')
                    ->create($paymentMethod);

                // 会員タイプを有料会員に変更
                $user->member_type = 'paid';
                $user->save();
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->route('payment.index')->with('error', '支払情報の登録に失敗しました: ' . $e->getMessage());
            }

        return redirect()->route('mypage')->with('message', '有料会員にアップグレードしました');
    }

    public function edit()
    {
        $user = Auth::user();

        try {
            // Stripeの顧客情報を取得
            $paymentMethod = $user->defaultPaymentMethod();
        } catch (Exception $e) {
            return redirect()->route('mypage')->with('error', 'カード情報の取得に失敗しました: ' . $e->getMessage());
        }
        return view('payment.edit', compact('user','paymentMethod'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $paymentMethod = $request->paymentMethod;

        try {
            // Stripeの顧客情報を作成または取得
            $user->createOrGetStripeCustomer();
            $user->updateDefaultPaymentMethod($paymentMethod);

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('payment.edit')->with('error', 'カード情報の更新に失敗しました: ' . $e->getMessage());
        }

        return redirect()->route('mypage')->with('message', 'カード情報が更新されました');
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        DB::beginTransaction();
        try {
            // サブスクリプションをキャンセル
            $user->subscription('default')->cancelNow();
            $user->member_type = 'free';
            $user->save();
            DB::commit();
            return redirect()->route('mypage')->with('message', '有料プランを解約しました');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('mypage')->with('error', '有料プランの解約に失敗しました: ' . $e->getMessage());
        }
    }
}
