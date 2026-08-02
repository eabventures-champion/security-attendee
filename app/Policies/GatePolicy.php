<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Gate;

class GatePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Gate $gate): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Gate $gate): bool { return true; }
    public function delete(User $user, Gate $gate): bool { return true; }
    public function assignRoles(User $user, Gate $gate): bool { return true; }
}