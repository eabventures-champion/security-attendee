<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VipWaitlistDiscountNotification extends Mailable
{
    use Queueable, SerializesModels;

    public string $subscriberEmail;
    public string $promoCode;
    public string $customMessage;
    public string $subjectText;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subscriberEmail, string $promoCode = 'ATTENDFLOW50VIP', string $customMessage = '', string $subjectText = '')
    {
        $this->subscriberEmail = $subscriberEmail;
        $this->promoCode = $promoCode;
        $this->customMessage = $customMessage;
        $this->subjectText = $subjectText ?: '🎉 Your AttendFlow VIP Early Access & 50% Off Promo Code!';
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.vip-waitlist-discount',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
