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

class AttendeePrivateInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public Attendee $attendee;
    public Event $event;
    public string $inviteUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Attendee $attendee)
    {
        $this->attendee = $attendee;
        $this->event = $attendee->event;

        // Generate invitation URL with unique QR token
        $qrCode = $attendee->qrCode;
        $token = $qrCode ? $qrCode->secure_token : $attendee->uuid;

        if ($this->event->is_private) {
            $this->inviteUrl = route('events.public.invite', ['event_slug' => $this->event->slug, 'token' => $token]);
        } else {
            $this->inviteUrl = route('events.public.register', ['event_slug' => $this->event->slug, 'token' => $token]);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectPrefix = $this->event->is_private ? '[Exclusive Invitation]' : '[Event Pass]';
        return new Envelope(
            subject: "{$subjectPrefix} Invitation to {$this->event->name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attendee-private-invitation',
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
