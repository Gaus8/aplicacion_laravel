<?php

namespace Tests\Feature;

use App\Mail\ContactSubmissionNotification;
use App\Models\ContactSubmission;
use App\Models\User;
use App\Services\SmtpMailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\PendingMail;
use Mockery;
use Tests\TestCase;

class ContactModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_stores_submission_and_sends_notification_through_shared_smtp_service(): void
    {
        config(['mail.from.address' => 'webmaster@example.test']);
        $mailer = Mockery::mock(Mailer::class);
        $pendingMail = Mockery::mock(PendingMail::class);
        $pendingMail->shouldReceive('send')->once()->with(Mockery::on(fn ($mail) => $mail instanceof ContactSubmissionNotification && $mail->submission->email === 'visitor@example.test'));
        $mailer->shouldReceive('to')->once()->with('webmaster@example.test')->andReturn($pendingMail);
        $smtp = Mockery::mock(SmtpMailer::class);
        $smtp->shouldReceive('mailer')->once()->andReturn($mailer);
        $this->app->instance(SmtpMailer::class, $smtp);

        $this->get(route('contact.create'))->assertOk()->assertSee('name="website"', false);
        $this->post(route('contact.store'), $this->payload())->assertRedirect(route('contact.create'));

        $submission = ContactSubmission::query()->firstOrFail();
        $this->assertSame('Consulta sobre servicios', $submission->subject);
        $this->assertNotEmpty($submission->ip_address);
        $this->assertDatabaseCount('contact_submissions', 1);
    }

    public function test_honeypot_is_silently_discarded_and_contact_is_rate_limited(): void
    {
        for ($attempt = 1; $attempt <= 4; $attempt++) {
            $this->post(route('contact.store'), $this->payload(['website' => 'spam']))
                ->assertRedirect(route('contact.create'))
                ->assertSessionHas('success');
        }
        $this->post(route('contact.store'), $this->payload(['website' => 'spam']))->assertTooManyRequests();
        $this->assertDatabaseCount('contact_submissions', 0);
    }

    public function test_contact_validation_and_private_inbox_permissions(): void
    {
        $this->from(route('contact.create'))->post(route('contact.store'), $this->payload(['email' => 'invalid']))
            ->assertRedirect(route('contact.create'))->assertSessionHasErrors('email');
        $this->get(route('admin.contact-messages.index'))->assertRedirect(route('login'));

        ContactSubmission::create(['name' => 'Visitante', 'email' => 'visitor@example.test', 'subject' => 'Ayuda', 'message' => 'Necesito ayuda con la plataforma.']);
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('admin.contact-messages.index'))->assertOk()->assertSeeText('Visitante');
        $message = ContactSubmission::query()->firstOrFail();
        $this->patch(route('admin.contact-messages.read', $message))->assertRedirect();
        $this->assertNotNull($message->fresh()->read_at);
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'name' => 'Visitante', 'email' => 'visitor@example.test',
            'subject' => 'Consulta sobre servicios', 'message' => 'Quisiera conocer más sobre los servicios.',
        ], $overrides);
    }
}
