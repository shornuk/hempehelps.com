<?php

namespace Tests\Feature\Auth;

use App\Auth\Models\MagicLinkToken;
use App\Livewire\Auth\OtpLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Email me a link');
    }

    public function test_magic_link_is_issued(): void
    {
        Mail::fake();

        Livewire::test(OtpLogin::class)
            ->set('email', 'buyer@example.com')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseHas('magic_link_tokens', ['email' => 'buyer@example.com']);
    }

    public function test_magic_link_logs_the_user_in(): void
    {
        $token = MagicLinkToken::issue('buyer@example.com');

        $this->get(route('login.verify', [
            'token' => $token->token,
            'email' => 'buyer@example.com',
        ]))->assertRedirect('/account');

        $this->assertAuthenticated();
        $this->assertNotNull(User::query()->where('email', 'buyer@example.com')->first()?->email_verified_at);
    }

    public function test_unverified_users_cannot_trigger_admin_order_lookups(): void
    {
        Http::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('account'))
            ->assertOk()
            ->assertSee('Verify your email');

        Http::assertNothingSent();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');

        $this->assertGuest();
    }
}
