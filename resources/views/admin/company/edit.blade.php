@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>会社情報の編集</h1>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('admin.company.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="mb-3">
            <label for="name" class="form-label">会社名</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $company->name ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">説明</label>
            <textarea name="description" class="form-control" rows="5" required>{{ old('description', $company->description ?? '') }}</textarea>
        </div>
        <div class="mb-3">
            <label for="postal_code" class="form-label">郵便番号</label>
            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $company->postal_code ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" class="form-control" value="{{ old('address', $company->address ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">電話番号</label>
            <input type="text" name="phone" class="form-control" value="{{ old('phone', $company->phone ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="url" class="form-label">URL</label>
            <input type="text" name="url" class="form-control" value="{{ old('url', $company->url ?? '') }}" required>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">会社の画像</label>
            <input type="file" name="image" class="form-control">
            @if (isset($company->image))
                <img src="{{ asset($company->image) }}" alt="Company Image" class="img-fluid mt-2" style="max-width: 200px;">
            @endif
        </div>
        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
