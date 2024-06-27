<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Stripe\PaymentMethod;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Stripeのモックを設定
        Http::fake([
            'https://api.stripe.com/v1/customers' => Http::response(['id' => 'cus_fake'], 200),
            'https://api.stripe.com/v1/payment_methods/pm_fake' => Http::response(['id' => 'pm_fake'], 200),
            'https://api.stripe.com/v1/payment_methods' => Http::response(['id' => 'pm_fake'], 200),
            'https://api.stripe.com/v1/subscriptions' => Http::response(['id' => 'sub_fake'], 200),
            'https://api.stripe.com/v1/subscriptions/sub_fake' => Http::response(['id' => 'sub_fake'], 200),
        ]);
    }

    public function testUserCanRegisterForPaidMembership()
    {
        $user = User::factory()->create(['member_type' => 'free']);

        $response = $this->actingAs($user)->post(route('payment.store'), [
            'paymentMethod' => 'pm_fake',
        ]);

        $response->assertRedirect(route('mypage'));
        $response->assertSessionHas('message', '有料会員にアップグレードしました');

        $user->refresh();
        $this->assertEquals('paid', $user->member_type);
    }

    public function testUserCanUpdatePaymentMethod()
    {
        $user = User::factory()->create(['member_type' => 'paid']);

        $response = $this->actingAs($user)->post(route('payment.update'), [
            'paymentMethod' => 'pm_fake',
        ]);

        $response->assertRedirect(route('mypage'));
        $response->assertSessionHas('message', 'カード情報が更新されました');
    }

    public function testUserCanCancelPaidMembership()
    {
        $user = User::factory()->create(['member_type' => 'paid']);

        // サブスクリプションを作成
        $subscription = $user->subscriptions()->create([
            'name' => 'default',
            'stripe_id' => 'sub_fake',
            'stripe_status' => 'active',
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post(route('payment.cancel'));

        $response->assertRedirect(route('mypage'));
        $response->assertSessionHas('message', '有料プランを解約しました');

        $user->refresh();
        $this->assertEquals('free', $user->member_type);
    }
}
