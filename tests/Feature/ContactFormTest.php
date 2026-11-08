<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_send_valid_contact_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '0790000000',
            'subject' => 'Product Inquiry',
            'message' => 'I would like to know more about your products.',
        ]);

        $response->assertRedirect(route('contact.index'));

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'customer@example.com',
            'subject' => 'Product Inquiry',
            'is_read' => false,
        ]);
    }

    public function test_invalid_contact_message_is_rejected(): void
    {
        $response = $this->post('/contact', [
            'name' => '',
            'email' => 'invalid-email',
            'subject' => '',
            'message' => 'short',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'email',
            'subject',
            'message',
        ]);

        $this->assertSame(0, ContactMessage::count());
    }
}
