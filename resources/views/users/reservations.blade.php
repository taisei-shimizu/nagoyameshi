@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- メッセージ -->
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <!-- パンくずリスト -->
    @component('components.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
    @endcomponent

    <h1>予約一覧</h1>

    <ul class="nav nav-tabs" id="reservationTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="future-tab" data-bs-toggle="tab" data-bs-target="#future" type="button" role="tab" aria-controls="future" aria-selected="true">予約</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab" aria-controls="past" aria-selected="false">過去の予約</button>
        </li>
    </ul>
    <div class="tab-content" id="reservationTabsContent">
        <div class="tab-pane fade show active" id="future" role="tabpanel" aria-labelledby="future-tab">
            @if ($futureReservations->isEmpty())
                <p>予約はありません。</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>店舗名</th>
                                <th>予約日</th>
                                <th>予約時間</th>
                                <th>人数</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($futureReservations as $reservation)
                                <tr>
                                    <td><a href="{{ route('shops.show', $reservation->shop) }}">{{ $reservation->shop->name }}</a></td>
                                    <td>{{ $reservation->reservation_date }}</td>
                                    <td>{{ $reservation->reservation_time }}</td>
                                    <td>{{ $reservation->number_of_people }}</td>
                                    <td>
                                        <form action="{{ route('reservations.destroy', $reservation) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('本当にキャンセルしますか？')">キャンセル</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        <div class="tab-pane fade" id="past" role="tabpanel" aria-labelledby="past-tab">
            @if ($pastReservations->isEmpty())
                <p>過去の予約はありません。</p>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>店舗名</th>
                                <th>予約日</th>
                                <th>予約時間</th>
                                <th>人数</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pastReservations as $reservation)
                                <tr>
                                    <td><a href="{{ route('shops.show', $reservation->shop) }}">{{ $reservation->shop->name }}</a></td>
                                    <td>{{ $reservation->reservation_date }}</td>
                                    <td>{{ $reservation->reservation_time }}</td>
                                    <td>{{ $reservation->number_of_people }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
