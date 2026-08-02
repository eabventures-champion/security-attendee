<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Attendee;
use App\Models\Event;

class EventRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public Attendee $attendee;
    public Event $event;

    /**
     * Create a new message instance.
     */
    public function __construct(Attendee $attendee)
    {
        $this->attendee = $attendee;
        $this->event = $attendee->event;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Registration Confirmation: ' . ($this->event->name ?? 'Event Registration'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event-registration-confirmation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
