<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        Mail::fake();
    }

    public function test_guest_can_open_the_form(): void
    {
        $this->getJson('/api/contact-messages/form')
            ->assertOk()
            ->assertJsonStructure(['data' => ['form_token']]);
    }

    public function test_guest_can_send_a_message(): void
    {
        $this->postMessage('ada@example.com', 'Ada Lovelace')
            ->assertCreated()
            ->assertJsonPath('message', 'Thanks, your message was sent.');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'ada@example.com',
            'name' => 'Ada Lovelace',
        ]);

        Mail::assertSent(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo(config('contact.inbox'))
                && $mail->hasReplyTo('ada@example.com')
                && $mail->contactMessage->message === 'Hello from the site.';
        });
    }

    public function test_a_filled_honeypot_is_dropped(): void
    {
        $this->postMessage('bot@example.com', 'Bot', ['company_website' => 'https://spam.test'])
            ->assertCreated()
            ->assertJsonPath('message', 'Thanks, your message was sent.');

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_a_message_sent_immediately_is_dropped(): void
    {
        $token = $this->getJson('/api/contact-messages/form')->json('data.form_token');

        $this->postJson('/api/contact-messages', [
            'name' => 'Fast Bot',
            'email' => 'fast@example.com',
            'message' => 'Hello from the site.',
            'form_token' => $token,
        ])->assertCreated();

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_an_expired_token_is_rejected(): void
    {
        $token = $this->getJson('/api/contact-messages/form')->json('data.form_token');
        $this->travel(2)->hours();

        $this->postJson('/api/contact-messages', [
            'name' => 'Late',
            'email' => 'late@example.com',
            'message' => 'Hello from the site.',
            'form_token' => $token,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors('form_token');

        $this->assertDatabaseCount('contact_messages', 0);
    }

    public function test_the_same_address_is_limited_to_one_message_per_window(): void
    {
        $this->postMessage('ada@example.com')->assertCreated();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.8'])
            ->postMessage('ada@example.com')
            ->assertTooManyRequests()
            ->assertJsonPath('message', 'Too many messages. Try again in a few minutes.');

        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_the_same_ip_is_limited_to_one_message_per_window(): void
    {
        $this->postMessage('one@example.com')->assertCreated();

        $this->postMessage('two@example.com')->assertTooManyRequests();

        $this->assertDatabaseCount('contact_messages', 1);
    }

    public function test_guest_cannot_read_the_inbox(): void
    {
        $this->getJson('/api/contact-messages')->assertUnauthorized();
    }

    public function test_owner_can_read_the_inbox(): void
    {
        $this->postMessage('ada@example.com', 'Ada Lovelace')->assertCreated();
        $user = User::factory()->create();
        $message = ContactMessage::query()->firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/contact-messages')
            ->assertOk()
            ->assertJsonPath('data.0.email', 'ada@example.com');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/contact-messages/'.$message->id)
            ->assertOk()
            ->assertJsonPath('data.name', 'Ada Lovelace');
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function postMessage(string $email, string $name = 'Ada Lovelace', array $extra = [])
    {
        $token = $this->getJson('/api/contact-messages/form')->json('data.form_token');
        $this->travel(4)->seconds();

        return $this->postJson('/api/contact-messages', [
            'name' => $name,
            'email' => $email,
            'message' => 'Hello from the site.',
            'form_token' => $token,
            ...$extra,
        ]);
    }
}
