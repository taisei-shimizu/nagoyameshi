@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>会社情報</h1>

    @if ($company)
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ $company->name }}</h5>
                <p class="card-text">{{ $company->description }}</p>
                <p class="card-text">住所:  {{ $company->postal_code }} {{ $company->address }}</p>
                <p class="card-text">電話番号: {{ $company->phone }}</p>
                <p class="card-text">URL: <a href="{{ $company->url }}" target="_blank">{{ $company->url }}</a></p>
                <p class="card-text"><img src="{{ asset($company->image) }}" alt="{{ $company->name }}" style="max-width: 500px;"></p>
            </div>
        </div>
    @else
        <p>会社情報が設定されていません。</p>
    @endif
</div>
@endsection
