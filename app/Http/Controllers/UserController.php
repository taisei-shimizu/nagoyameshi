<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $user = Auth::user();
        $breadcrumbs = [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('mypage.edit'), 'label' => '会員情報編集']
        ];
        return view('users.edit', compact('user', 'breadcrumbs'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.Auth::id()],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->name = $request->input('name') ? $request->input('name') : $user->name;
        $user->email = $request->input('email') ? $request->input('email') : $user->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('mypage.edit')->with('message', 'ユーザー情報が更新されました。');

    }

    // マイページ表示
    public function mypage()
    {
        $user = Auth::user();
        return view('users.mypage', compact('user'));
    }
    // お気に入り一覧表示
    public function favorites()
    {
        $user = Auth::user();
        $breadcrumbs = [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('mypage.favorites'), 'label' => 'お気に入り一覧']
        ];
        $favorites = $user->favorites;
        return view('users.favorites', compact('favorites', 'breadcrumbs'));
    }

    // 予約一覧表示
    public function reservations()
    {
        $user = Auth::user();
        $currentDate = Carbon::today();
        $currentTime = Carbon::now()->format('H:i');
        // 過去の予約
        $pastReservations = $user->reservations()
        ->where(function ($query) use ($currentDate, $currentTime) {
            $query->where('reservation_date', '<', $currentDate)
                  ->orWhere(function ($query) use ($currentDate, $currentTime) {
                      $query->where('reservation_date', '=', $currentDate)
                            ->where('reservation_time', '<', $currentTime);
                  });
        })
        ->with('shop')
        ->get();

        // 未来の予約
        $futureReservations = $user->reservations()
        ->where(function ($query) use ($currentDate, $currentTime) {
            $query->where('reservation_date', '>', $currentDate)
                  ->orWhere(function ($query) use ($currentDate, $currentTime) {
                      $query->where('reservation_date', '=', $currentDate)
                            ->where('reservation_time', '>=', $currentTime);
                  });
        })
        ->with('shop')
        ->get();

        $breadcrumbs = [
            ['url' => route('mypage'), 'label' => 'マイページ'],
            ['url' => route('mypage.reservations'), 'label' => '予約一覧']
        ];
        $reservations = $user->reservations;
        return view('users.reservations', compact('pastReservations', 'futureReservations', 'breadcrumbs'));
    }
    // 退会処理
    public function destroy(Request $request)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            // 有料会員の場合、サブスクリプションをキャンセル
            if ($user->member_type == 'paid') {
                if($user->subscribed('default')) {
                    $user->subscription('default')->cancelNow();
                }
                $user->member_type = 'free';
                $user->save();
            }
            // ユーザーを削除
            $user->delete();

            DB::commit();

            // リダイレクトとメッセージを設定
            $redirect = redirect('/')->with('message', '退会しました。');

            Auth::logout();

            return $redirect;

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('error', '退会処理に失敗しました。');
        }
    }
}
