<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CompanyManagementController extends Controller
{
    public function show()
    {
        $company = Company::first();
        return view('company', compact('company'));
    }

    public function edit()
    {
        $company = Company::first();
        return view('admin.company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'url' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $company = Company::first();

        if ($company) {
            $company->fill($request->all());
        } else {
            $company = new Company($request->all());
        }

        if ($request->hasFile('image')) {
            $oldImage = $company->image;
            $imagePath = $request->file('image')->store('', 'images');
            $company->image = $imagePath;
            //dd($oldImage, $imagePath);
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
        }

        $company->save();

        return redirect()->route('admin.company.edit')->with('message', '会社情報を更新しました。');
    }
}
