<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

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

    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->all());

        return redirect()->route('admin.categories.index')->with('message', 'カテゴリを作成しました。');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->all());

        return redirect()->route('admin.categories.index')->with('message', 'カテゴリを更新しました。');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('message', 'カテゴリを削除しました。');
    }
}
