<?php

namespace App\Mail;

use App\Models\VirtualIdCard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MemberVirtualIdCardMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public VirtualIdCard $card;
    public string $cardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(VirtualIdCard $card)
    {
        $this->card = $card;
        $this->cardUrl = route('virtual-cards.public.view', ['uuid' => $card->uuid]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $orgName = $this->card->organization ? $this->card->organization->name : config('app.name');
        return new Envelope(
            subject: "🪪 Your Official Virtual ID Card — {$this->card->full_name} ({$this->card->member_id_number})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.member-virtual-id-card',
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
