<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Organization;

class NewAccountRegistrationNotification extends Notification
{
    use Queueable;

    public User $applicant;
    public Organization $organization;

    public function __construct(User $applicant, Organization $organization)
    {
        $this->applicant = $applicant;
        $this->organization = $organization;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'registration',
            'title' => 'New Organization Account Registration',
            'message' => "{$this->applicant->name} created account for {$this->organization->name} ({$this->applicant->email}) and requires Super Admin review.",
            'link' => route('users.index'),
            'organization_id' => $this->organization->id,
            'user_id' => $this->applicant->id,
        ];
    }
}
