<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Event;

class EventPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Event $event): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Event $event): bool { return true; }
    public function delete(User $user, Event $event): bool { return true; }
    public function publish(User $user, Event $event): bool { return true; }
    public function archive(User $user, Event $event): bool { return true; }
    public function cancel(User $user, Event $event): bool { return true; }
    public function duplicate(User $user, Event $event): bool { return true; }
    public function manageAttendees(User $user, Event $event): bool { return true; }
    public function manageGates(User $user, Event $event): bool { return true; }
    public function viewReports(User $user, Event $event): bool { return true; }
}