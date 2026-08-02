<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OrgAdminActivatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public User $orgAdmin;
    public string $confirmUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(User $orgAdmin)
    {
        $this->orgAdmin = $orgAdmin;
        $token = $orgAdmin->approval_token ?: $orgAdmin->invitation_token;
        if (!$token) {
            $token = (string) \Illuminate\Support\Str::uuid();
            $orgAdmin->approval_token = $token;
            $orgAdmin->save();
        }
        $this->confirmUrl = route('invitation.accept', ['token' => $token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🎉 Your AttendFlow Workspace Has Been Approved & Activated!",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.org-admin-activated',
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
