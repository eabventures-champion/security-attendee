<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class TeamMemberInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $rawPassword;
    public string $inviteUrl;
    public string $roleLabel;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $rawPassword = '')
    {
        $this->user = $user;
        $this->rawPassword = $rawPassword;
        $this->roleLabel = $user->role_label;
        $this->inviteUrl = route('team.accept_invite', ['token' => $user->invitation_token]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name', 'AttendFlow');
        return new Envelope(
            subject: "Invitation to Join Team on {$appName} - {$this->roleLabel}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.team-member-invitation',
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
