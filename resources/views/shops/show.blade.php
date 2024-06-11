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
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <img src="{{ asset($shop->image) }}" class="card-img-top" alt="{{ $shop->name }}">
                <div class="card-body">
                    <h5 class="card-title">{{ $shop->name }}</h5>
                    <p class="card-text">{{ $shop->description }}</p>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>カテゴリ:</strong> {{ $shop->category->name }}</li>
                        <li class="list-group-item"><strong>予算:</strong> {{ $shop->budget_lower }} - {{ $shop->budget_upper }} 円</li>
                        <li class="list-group-item"><strong>営業時間:</strong> {{ $shop->opening_time }} - {{ $shop->closing_time }}</li>
                        <li class="list-group-item"><strong>定休日:</strong> {{ $shop->closed_day }}</li>
                        <li class="list-group-item"><strong>郵便番号:</strong> {{ $shop->postal_code }}</li>
                        <li class="list-group-item"><strong>住所:</strong> {{ $shop->address }}</li>
                        <li class="list-group-item"><strong>電話番号:</strong> {{ $shop->phone }}</li>
                        <li class="list-group-item"><strong>評価:</strong>
                            @if ($averageRating)
                                {{ number_format($averageRating, 1) }} / 5
                            @else
                                まだ評価はありません
                            @endif
                        </li>
                    </ul>

                    <!-- お気に入り登録・解除ボタン -->
                    @auth
                        @if (Auth::user()->member_type === 'paid')
                            @if (Auth::user()->favorites->contains('shop_id', $shop->id))
                                <form action="{{ route('favorites.destroy', $shop) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-heart text-dark"></i> お気に入り解除
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('favorites.store', $shop) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="far fa-heart text-light"></i> お気に入り
                                    </button>
                                </form>
                            @endif
                        @endif
                    @endauth
                </div>
            </div>

            <!-- 予約ボタン -->
            @auth
                @if (Auth::user()->member_type === 'paid')
                    <div class="my-4">
                        <h5>予約</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reservationModal">
                            予約する
                        </button>
                    </div>
                @endif
            @endauth

            <!-- 予約モーダル -->
            <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reservationModalLabel">予約</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('reservations.store', $shop) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="reservation_date" class="form-label">予約日</label>
                                    <input type="date" class="form-control @error('reservation_date') is-invalid @enderror" id="reservation_date" name="reservation_date" value="{{ old('reservation_date') }}" required>
                                    @error('reservation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="reservation_time" class="form-label">予約時間</label>
                                    <select class="form-control @error('reservation_time') is-invalid @enderror" id="reservation_time" name="reservation_time" required>
                                        @foreach ($timeSlots as $time)
                                            <option value="{{ $time }}">{{ $time }}</option>
                                        @endforeach
                                    </select>
                                    @error('reservation_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="number_of_people" class="form-label">人数</label>
                                    <input type="number" class="form-control @error('number_of_people') is-invalid @enderror" id="number_of_people" name="number_of_people" value="{{ old('number_of_people') }}" required>
                                    @error('number_of_people')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">予約する</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- レビュー投稿フォーム -->
            <h5>レビュー</h5>
            @auth
                @if (Auth::user()->member_type === 'paid')
                    <div class="my-4">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reviewModal">
                            レビュー投稿
                        </button>
                    </div>
                @endif
            @endauth

            <!-- レビュー投稿モーダル -->
            <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reviewModalLabel">レビュー投稿</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="POST" action="{{ route('reviews.store', $shop) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="score" class="form-label">評価</label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="score" value="5" required><label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star4" name="score" value="4"><label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star3" name="score" value="3"><label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star2" name="score" value="2"><label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                                        <input type="radio" id="star1" name="score" value="1"><label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">レビュー内容</label>
                                    <textarea class="form-control" id="content" name="content" rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success">投稿</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- レビューページ -->
            <div class="my-4">
                @foreach ($reviews as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->score)
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-warning"></i>
                                    @endif
                                @endfor
                            </h5>
                            <p class="card-text">{{ $review->content }}</p>
                            <p class="card-text"><small class="text-muted">投稿者: {{ $review->user->name }}</small></p>
                            @if (Auth::id() === $review->user_id)
                                <!-- 編集ボタン -->
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editReviewModal-{{ $review->id }}">
                                    編集
                                </button>
                                <!-- 削除ボタン -->
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteReviewModal-{{ $review->id }}">
                                    削除
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- レビュー編集モーダル -->
                    <div class="modal fade" id="editReviewModal-{{ $review->id }}" tabindex="-1" aria-labelledby="editReviewModalLabel-{{ $review->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editReviewModalLabel-{{ $review->id }}">レビュー編集</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="{{ route('reviews.update', $review) }}">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-3">
                                            <label for="score" class="form-label">評価</label>
                                            <div class="star-rating">
                                                <input type="radio" id="edit-star5-{{ $review->id }}" name="score" value="5" {{ $review->score == 5 ? 'checked' : '' }}><label for="edit-star5-{{ $review->id }}" title="5 stars"><i class="fas fa-star"></i></label>
                                                <input type="radio" id="edit-star4-{{ $review->id }}" name="score" value="4" {{ $review->score == 4 ? 'checked' : '' }}><label for="edit-star4-{{ $review->id }}" title="4 stars"><i class="fas fa-star"></i></label>
                                                <input type="radio" id="edit-star3-{{ $review->id }}" name="score" value="3" {{ $review->score == 3 ? 'checked' : '' }}><label for="edit-star3-{{ $review->id }}" title="3 stars"><i class="fas fa-star"></i></label>
                                                <input type="radio" id="edit-star2-{{ $review->id }}" name="score" value="2" {{ $review->score == 2 ? 'checked' : '' }}><label for="edit-star2-{{ $review->id }}" title="2 stars"><i class="fas fa-star"></i></label>
                                                <input type="radio" id="edit-star1-{{ $review->id }}" name="score" value="1" {{ $review->score == 1 ? 'checked' : '' }}><label for="edit-star1-{{ $review->id }}" title="1 star"><i class="fas fa-star"></i></label>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="content" class="form-label">レビュー内容</label>
                                            <textarea class="form-control" id="content" name="content" rows="3" required>{{ $review->content }}</textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success">更新</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- レビュー削除確認モーダル -->
                    <div class="modal fade" id="deleteReviewModal-{{ $review->id }}" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">レビューを削除しますか？</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                    <form method="POST" action="{{ route('reviews.destroy', $review) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger">削除</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 今日より前の日付を選択できないようにする
        var today = new Date().toISOString().split('T')[0];
        document.getElementById('reservation_date').setAttribute('min', today);
    });

    // 30分刻みで予約時間を選択できるようにする
    document.getElementById('reservation_time').setAttribute('step', 1800);
</script>
@endsection
