<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Attendee;

class AttendeePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Attendee $attendee): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Attendee $attendee): bool { return true; }
    public function delete(User $user, Attendee $attendee): bool { return true; }
    public function verify(User $user, Attendee $attendee): bool { return true; }
    public function checkIn(User $user, Attendee $attendee): bool { return true; }
    public function downloadQr(User $user, Attendee $attendee): bool { return true; }
}