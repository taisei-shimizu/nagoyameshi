<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('category_name')) {
            $query->byName($request->input('category_name'));
        }

        $total = $query->count(); // 総件数の取得
        $categories = $query->paginate(15);

        return view('admin.categories.index', compact('categories', 'total'));
    }


    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
        ]);

        Category::create($request->all());

        return redirect()->route('admin.categories.index')->with('message', 'カテゴリを作成しました。');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        $category->update($request->all());

        return redirect()->route('admin.categories.index')->with('message', 'カテゴリを更新しました。');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('message', 'カテゴリを削除しました。');
    }
}
