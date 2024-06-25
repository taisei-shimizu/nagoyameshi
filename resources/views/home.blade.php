@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- メッセージ -->
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <!-- KVセクション -->
    <div class="kv">
        <div>
            <h1 class="display-4">名古屋の美味しいB級グルメが集結！</h1>
        </div>
        <div id="shopCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner">
                @foreach ($shops->chunk(3) as $index => $chunk)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        <div class="row">
                            @foreach ($chunk as $shop)
                                <div class="col-md-4">
                                    <div class="card h-100">
                                        <img src="{{ asset($shop->image) }}" class="card-img-top" alt="{{ $shop->name }}" style="height: 300px; object-fit: cover;">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    

    <!-- 特徴セクション -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">NOGOYAMESHIの特徴</h2>
            <div class="row features">
                <div class="col-md-4 text-center feature-box">
                    <i class="features-icon fas fa-utensils"></i>
                    <h3>豊富な店舗</h3>
                    <p>名古屋のB級グルメ名店を網羅。お気に入りの店舗を見つけましょう。</p>
                </div>
                <div class="col-md-4 text-center feature-box">
                    <i class="features-icon fas fa-calendar-alt"></i>
                    <h3>予約検索</h3>
                    <p>予約機能で名店に簡単に予約できます。</p>
                </div>
                <div class="col-md-4 text-center feature-box">
                    <i class="features-icon fas fa-star"></i>
                    <h3>レビュー機能</h3>
                    <p>他のユーザーのレビューを参考にして、お店選びに役立てましょう。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- お客様の声セクション -->
    <section id="testimonials" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">お客様の声</h2>
            <div class="row">
            <div class="col-md-4">
                    <div class="testimonial-box">
                        <img src="{{ asset('images/customer01.svg') }}" class="testimonial-img" alt="山田 太郎">
                        <blockquote class="blockquote">
                            <p class="mb-0">このアプリで名古屋の美味しいお店をたくさん見つけました！</p>
                            <footer class="blockquote-footer">山田 太郎</footer>
                        </blockquote>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-box">
                        <img src="{{ asset('images/customer02.svg') }}" class="testimonial-img" alt="鈴木 花子">
                        <blockquote class="blockquote">
                            <p class="mb-0">レビュー機能が便利で、お店選びに役立ちます。</p>
                            <footer class="blockquote-footer">鈴木 花子</footer>
                        </blockquote>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-box">
                        <img src="{{ asset('images/customer03.svg') }}" class="testimonial-img" alt="佐藤 次郎">
                        <blockquote class="blockquote">
                            <p class="mb-0">検索機能で近くの美味しいお店がすぐ見つかります。</p>
                            <footer class="blockquote-footer">佐藤 次郎</footer>
                        </blockquote>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- お問い合わせセクション -->
    <section id="contact" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">お問い合わせ</h2>
            <form>
                <div class="form-group">
                    <label for="name">お名前</label>
                    <input type="text" class="form-control" id="name" placeholder="お名前を入力してください">
                </div>
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" class="form-control" id="email" placeholder="メールアドレスを入力してください">
                </div>
                <div class="form-group">
                    <label for="message">メッセージ</label>
                    <textarea class="form-control" id="message" rows="4" placeholder="メッセージを入力してください"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">送信</button>
            </form>
        </div>
    </section>
</div>
@endsection
