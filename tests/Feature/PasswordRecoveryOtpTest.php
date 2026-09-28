<?php

namespace Tests\Feature;

use App\Models\PasswordRecoveryOtp;
use App\Models\User;
use App\Services\SmtpMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class PasswordRecoveryOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_links_to_the_password_recovery_flow(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('password.request'), false);
    }

    public function test_recovery_request_and_code_verification_do_not_reveal_unknown_accounts(): void
    {
        $smtp = Mockery::mock(SmtpMailer::class);
        $smtp->shouldNotReceive('mailer');
        $this->app->instance(SmtpMailer::class, $smtp);

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown@example.test'])
            ->assertRedirect(route('password.otp.form'))
            ->assertSessionHas('status', 'Si existe una cuenta para ese correo, recibirás un código para continuar.');

        $this->assertDatabaseCount('password_recovery_otps', 0);
        $this->get(route('password.otp.form'))->assertOk()->assertSeeText('Si existe una cuenta para este correo');
        $this->post(route('password.otp.verify'), ['code' => '123456'])
            ->assertRedirect()
            ->assertSessionHasErrors('code');
    }

    public function test_registered_user_can_reset_password_with_a_single_use_email_code(): void
    {
        $user = User::factory()->create(['email' => 'person@example.test']);
        $this->mockSmtpDelivery($user->email, $code);

        $this->post(route('password.email'), ['email' => strtoupper($user->email)])
            ->assertRedirect(route('password.otp.form'))
            ->assertSessionHas('status');

        $challenge = PasswordRecoveryOtp::query()->firstOrFail();
        $this->assertNotSame($code, $challenge->code_hash);
        $this->assertTrue(Hash::check($code, $challenge->code_hash));
        $this->assertSame(0, $challenge->attempts);

        $this->post(route('password.otp.verify'), ['code' => $code])
            ->assertRedirect(route('password.reset.form'));
        $this->get(route('password.reset.form'))->assertOk()->assertSeeText('Crea una contraseña nueva');

        $this->put(route('password.update'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'))->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertDatabaseCount('password_recovery_otps', 0);
        $this->assertFalse(session()->has('password_recovery.email'));
    }

    public function test_invalid_codes_are_limited_and_locked_after_five_attempts(): void
    {
        $user = User::factory()->create();
        $this->mockSmtpDelivery($user->email, $validCode);
        $this->post(route('password.email'), ['email' => $user->email]);

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('password.otp.verify'), ['code' => '000000'])
                ->assertSessionHasErrors('code');
        }

        $this->assertSame(5, PasswordRecoveryOtp::query()->firstOrFail()->attempts);
        $this->post(route('password.otp.verify'), ['code' => $validCode])
            ->assertSessionHasErrors('code');
    }

    public function test_expired_code_cannot_be_used(): void
    {
        $user = User::factory()->create();
        $this->mockSmtpDelivery($user->email, $code);
        $this->post(route('password.email'), ['email' => $user->email]);
        $this->travel(6)->minutes();

        $this->post(route('password.otp.verify'), ['code' => $code])
            ->assertSessionHasErrors('code');
        $this->get(route('password.reset.form'))->assertRedirect(route('password.request'));
    }

    public function test_reset_endpoint_requires_a_verified_unexpired_challenge(): void
    {
        User::factory()->create();

        $this->put(route('password.update'), [
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertForbidden();
    }

    private function mockSmtpDelivery(string $recipient, ?string &$code): void
    {
        $mailer = Mockery::mock(Mailer::class);
        $mailer->shouldReceive('raw')->once()->withArgs(function (string $body, callable $callback) use ($recipient, &$code): bool {
            preg_match('/es: (\d{6})/', $body, $matches);
            $code = $matches[1] ?? null;

            $message = Mockery::mock(Message::class);
            $message->shouldReceive('to')->once()->with($recipient)->andReturnSelf();
            $message->shouldReceive('subject')->once()->with('Código para recuperar tu contraseña')->andReturnSelf();
            $callback($message);

            return $code !== null;
        });

        $smtp = Mockery::mock(SmtpMailer::class);
        $smtp->shouldReceive('configured')->andReturn(true);
        $smtp->shouldReceive('mailer')->once()->andReturn($mailer);
        $this->app->instance(SmtpMailer::class, $smtp);
    }
}
