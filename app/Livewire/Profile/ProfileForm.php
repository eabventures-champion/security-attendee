<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[Layout('layouts.app')]
#[Title('My Profile')]
class ProfileForm extends Component
{
    // Profile Info
    public string $name = '';
    public string $email = '';
    public string $phone = '';

    // Password Update
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
    }

    public function updateProfile(): void
    {
        $user = auth()->user();

        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => ['nullable', 'string', 'regex:/^[0-9]{10}$/'],
        ], [
            'phone.regex' => 'Phone number must be exactly 10 digits (e.g. 0246345698).',
        ]);

        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone = $this->phone;
        $user->save();

        session()->flash('profile_success', 'Profile information updated successfully.');
    }

    public function updatePassword(): void
    {
        $user = auth()->user();

        $this->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.current_password' => 'The provided password does not match your current password.',
            'new_password.confirmed' => 'New password confirmation does not match.',
            'new_password.min' => 'New password must be at least 8 characters long.',
        ]);

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        session()->flash('password_success', 'Security password changed successfully.');
    }

    public function render()
    {
        return view('livewire.profile.profile-form');
    }
}
