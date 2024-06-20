@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>管理者ダッシュボード</h1>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-primary w-100">管理者管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100">会員管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.shops.index') }}" class="btn btn-primary w-100">店舗管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-primary w-100">カテゴリ管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mt-4">
                <div class="card-body">
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-primary w-100">レビュー管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mt-4">
                <div class="card-body">
                    <a href="{{ route('admin.sales.index') }}" class="btn btn-primary w-100">売上管理</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card mt-4">
                <div class="card-body">
                    <a href="{{ route('admin.company.edit') }}" class="btn btn-primary w-100">会社情報管理</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    売上グラフ
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    会員数グラフ
                </div>
                <div class="card-body">
                    <canvas id="membersChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 売上グラフのデータ
    var salesData = {
        labels: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'],
        datasets: [{
            label: '売上',
            backgroundColor: 'rgba(0, 123, 255, 0.5)',
            borderColor: 'rgba(0, 123, 255, 1)',
            data: @json($sales)
        }]
    };

    // 会員数グラフのデータ
    var membersData = {
        labels: @json($months),
        datasets: [{
            label: '会員数',
            backgroundColor: 'rgba(255, 193, 7, 0.5)',
            borderColor: 'rgba(255, 193, 7, 1)',
            data: @json($counts)
        }]
    };

    // 売上グラフの生成
    var ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'bar',
        data: salesData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });

    // 会員数グラフの生成
    var ctxMembers = document.getElementById('membersChart').getContext('2d');
    new Chart(ctxMembers, {
        type: 'bar',
        data: membersData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
@endpush
