@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- パンくずリスト -->
    @component('components.breadcrumbs')
        @slot('breadcrumbs', [
            ['url' => route('admin.dashboard'), 'label' => 'ダッシュボード'],
            ['url' => route('admin.sales.index'), 'label' => '売上管理']
        ])
    @endcomponent
    <!-- 売上検索フォーム -->
    <form method="GET" action="{{ route('admin.sales.index') }}" class="mb-4">
        <div class="row">
            <div class="col-md-5">
                <label for="start_date">開始日</label>
                <input type="date" id="start_date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-5">
                <label for="end_date">終了日</label>
                <input type="date" id="end_date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary">検索</button>
            </div>
        </div>
    </form>

    <h2>売上一覧</h2>

    <p>検索結果: 全 {{ $totalCharges }} 件中 {{ $chargesPaginated->firstItem() }} 件〜{{ $chargesPaginated->lastItem() }} 件を表示</p>
    <p>合計金額: ¥{{ number_format($totalSales) }}</p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>ユーザーID</th>
                <th>ユーザー名</th>
                <th>金額</th>
                <th>支払日</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chargesPaginated as $charge)
            <tr>
                <td>{{ $charge['id'] }}</td>
                <td>{{ $charge['user_id'] }}</td>
                <td>{{ $charge['user_name'] }}</td>
                <td>{{ number_format($charge['amount']) }} 円</td>
                <td>{{ \Carbon\Carbon::createFromTimestamp($charge['created'])->format('Y-m-d H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center">
        {{ $chargesPaginated->links() }}
    </div>
</div>
@endsection
