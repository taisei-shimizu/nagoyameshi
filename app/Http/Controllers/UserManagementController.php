<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Exception;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin');

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('member_type')) {
            $query->where('member_type', $request->input('member_type'));
        }
        $total = $query->count();

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users', 'total'));
    }

    public function export(Request $request)
    {
        $query = User::where('role', '!=', 'admin'); // 管理者を除外

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->input('email') . '%');
        }

        if ($request->filled('member_type')) {
            $query->where('member_type', $request->input('member_type'));
        }

        $users = $query->get();

        // CSVデータの生成
        $csvData = [];
        $csvData[] = ['ID', '名前', '会員種別', 'メールアドレス', '登録日'];

        foreach ($users as $user) {
            $csvData[] = [
                $user->id,
                $user->name,
                $user->member_type === 'paid' ? '有料会員' : '無料会員',
                $user->email,
                $user->created_at->format('Y-m-d'),
            ];
        }

        // 一時ファイルに書き込む
        $file = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            fputcsv($file, $line);
        }
        rewind($file);
        $csvContent = stream_get_contents($file);
        fclose($file);

        // レスポンスを生成
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="users.csv"',
        ]);
    }

        // 退会処理
        public function destroy(User $user)
        {
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
                return redirect()->route('admin.users.index')->with('message', 'ユーザーを退会させました。');
            } catch (Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', '退会処理に失敗しました。');
            }
        }
}
