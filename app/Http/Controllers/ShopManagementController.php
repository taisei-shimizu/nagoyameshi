<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;


class ShopManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::with('category'); // カテゴリ情報を取得

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $total = $query->count(); // 総件数の取得
        $shops = $query->paginate(15);
        $categories = Category::all();

        return view('admin.shops.index', compact('shops', 'total', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.shops.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'budget_lower' => 'required|integer',
            'budget_upper' => 'required|integer',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'closed_day' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $shop = new Shop($request->all());

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('', 'images');
            $shop->image = 'images/' .$imagePath;
        }

        $shop->save();

        return redirect()->route('admin.shops.index')->with('message', '店舗を登録しました。');
    }

    public function edit(Shop $shop)
    {
        $categories = Category::all();
        return view('admin.shops.edit', compact('shop', 'categories'));
    }

    public function update(Request $request, Shop $shop)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'budget_lower' => 'required|integer',
            'budget_upper' => 'required|integer',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'closed_day' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // 古い画像のパスを取得
        $oldImage = $shop->image;

        $shop->fill($request->all());

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('', 'images');
            $shop->image = 'images/' .$imagePath;
            // 古い画像を削除
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $shop->save();

        return redirect()->route('admin.shops.index')->with('message', '店舗情報を更新しました。');
    }

    public function destroy(Shop $shop)
    {
        $shop->delete();

        return redirect()->route('admin.shops.index')->with('message', '店舗を削除しました。');
    }

    public function export(Request $request)
    {
        $query = Shop::query()->with('category');;
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $shops = $query->get();

        // CSVデータの生成
        $csvData = [];
        $csvData[] = ['ID', '店舗名', 'カテゴリ名','説明', '予算（下限）', '予算（上限）', '営業時間（開始）', '営業時間（終了）', '定休日', '郵便番号', '住所', '電話番号', '登録日'];

        foreach ($shops as $shop) {
            $csvData[] = [
                $shop->id,
                $shop->name,
                $shop->category->name,
                $shop->description,
                $shop->budget_lower,
                $shop->budget_upper,
                $shop->opening_time,
                $shop->closing_time,
                $shop->closed_day,
                $shop->postal_code,
                $shop->address,
                $shop->phone,
                $shop->created_at->format('Y-m-d'),
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
            'Content-Disposition' => 'attachment; filename="shops.csv"',
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();

        $config = new LexerConfig();
        $lexer = new Lexer($config);
        $interpreter = new Interpreter();
        $interpreter->addObserver(function(array $row) use (&$errors) {
            // 最初の行をスキップ
            static $firstRow = true;
            if ($firstRow) {
                $firstRow = false;
                return;
            }
            // カテゴリ名からカテゴリIDを取得
            $category = Category::where('name', $row[1])->first();

            if (!$category) {
                $errors[] = "カテゴリ名 '{$row[1]}' が見つかりません。";
                return;
            }

            try {
                Shop::create([
                    'name' => $row[0],
                    'category_id' => $category->id,
                    'description' => $row[2],
                    'budget_lower' => $row[3],
                    'budget_upper' => $row[4],
                    'opening_time' => $row[5],
                    'closing_time' => $row[6],
                    'closed_day' => $row[7],
                    'postal_code' => $row[8],
                    'address' => $row[9],
                    'phone' => $row[10],
                    'image' => 'images/sample.jpg', // 画像を sample.jpg に設定
                    'created_at' => $row[11],
                ]);
            } catch (\Exception $e) {
                $errors[] = 'CSVインポートに失敗しました。エラー: ' . $e->getMessage();
                Log::error('CSVインポートエラー: ' . $e->getMessage());
            }
        });

        try {
            $lexer->parse($path, $interpreter);
        } catch (\Exception $e) {
            $errors[] = 'CSVファイルのパース中にエラーが発生しました。エラー: ' . $e->getMessage();
            Log::error('CSVパースエラー: ' . $e->getMessage());
        }

        if (!empty($errors)) {
            return redirect()->back()->with('error', implode(', ', $errors));
        }

        return redirect()->route('admin.shops.index')->with('message', 'CSVファイルからのインポートが完了しました。');
    }

    //インポート用のテンプレートダウンロード
    public function downloadTemplate()
    {
        $csvContent = "店舗名,カテゴリ名,説明,予算（下限）,予算（上限）,営業時間（開始）,営業時間（終了）,定休日,郵便番号,住所,電話番号,登録日\n";
        $csvContent .= "サンプル店舗1,味噌カツ,店舗1の説明,1000,3000,09:00,18:00,日曜日,123-4567,東京都日本,012-345-6789,2023-01-01\n";
        $csvContent .= "サンプル店舗2,手羽先,店舗2の説明,2000,4000,10:00,19:00,土曜日,234-5678,大阪府日本,987-654-3210,2023-01-02\n";

        $fileName = "shop_template.csv";

        $response = Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ]);

        return $response;
    }

}
