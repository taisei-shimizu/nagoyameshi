@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.shops.index'), 'label' => '店舗管理'],
            ['url' => route('admin.shops.create'), 'label' => '店舗登録']
        ])
    @endcomponent
    <h1>店舗新規登録</h1>

    <form method="POST" action="{{ route('admin.shops.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">店舗名</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $shop->name ?? '') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">説明</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" required>{{ old('description', $shop->description ?? '') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category_id" class="form-label">カテゴリ</label>
            <select class="form-control" id="category_id" name="category_id" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="budget_lower" class="form-label">予算（下限）</label>
            <input type="number" class="form-control @error('budget_lower') is-invalid @enderror" id="budget_lower" name="budget_lower" value="{{ old('budget_lower', $shop->budget_lower ?? '') }}" required>
            @error('budget_lower')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="budget_upper" class="form-label">予算（上限）</label>
            <input type="number" class="form-control @error('budget_upper') is-invalid @enderror" id="budget_upper" name="budget_upper" value="{{ old('budget_upper', $shop->budget_upper ?? '') }}" required>
            @error('budget_upper')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="opening_time" class="form-label">営業時間（開始）</label>
            <input type="time" class="form-control @error('opening_time') is-invalid @enderror" id="opening_time" name="opening_time" value="{{ old('opening_time', $shop->opening_time ?? '') }}" required>
            @error('opening_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="closing_time" class="form-label">営業時間（終了）</label>
            <input type="time" class="form-control @error('closing_time') is-invalid @enderror" id="closing_time" name="closing_time" value="{{ old('closing_time', $shop->closing_time ?? '') }}" required>
            @error('closing_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="closed_day" class="form-label">定休日</label>
            <input type="text" class="form-control @error('closed_day') is-invalid @enderror" id="closed_day" name="closed_day" value="{{ old('closed_day', $shop->closed_day ?? '') }}" required>
            @error('closed_day')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', $shop->postal_code ?? '') }}" required>
            @error('postal_code')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $shop->address ?? '') }}" required>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">電話番号</label>
            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $shop->phone ?? '') }}" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">画像</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" {{ isset($shop) ? '' : 'required' }}>
            @error('image')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
    </form>
</div>
@endsection
