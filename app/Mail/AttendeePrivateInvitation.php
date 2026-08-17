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

class AttendeePrivateInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Attendee $attendee;
    public Event $event;
    public string $inviteUrl;
    public string $inviteType; // 'form' or 'direct'

    /**
     * Create a new message instance.
     */
    public function __construct(Attendee $attendee, string $inviteType = 'form')
    {
        $this->attendee = $attendee;
        $this->event = $attendee->event;
        $this->inviteType = $inviteType;

        // Generate invitation URL with unique QR token and parameters
        $qrCode = $attendee->qrCode;
        $token = $qrCode ? $qrCode->secure_token : $attendee->uuid;

        $queryParams = [
            'token' => $token,
            'email' => $attendee->email,
        ];

        if (!empty($attendee->full_name) && !str_starts_with(strtolower($attendee->full_name), 'guest')) {
            $queryParams['name'] = $attendee->full_name;
        }

        if ($this->inviteType === 'direct') {
            $queryParams['direct'] = '1';
        }

        if ($this->event->is_private) {
            $this->inviteUrl = route('events.public.invite', array_merge(['event_slug' => $this->event->slug], $queryParams));
        } else {
            $this->inviteUrl = route('events.public.register', array_merge(['event_slug' => $this->event->slug], $queryParams));
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
