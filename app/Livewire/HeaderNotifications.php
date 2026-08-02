<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class HeaderNotifications extends Component
{
    public bool $open = false;

    protected $listeners = ['notification-received' => '$refresh'];

    public function markAsRead(string $id, ?string $link = null)
    {
        DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_id', auth()->id())
            ->update(['read_at' => now()]);

        if ($link) {
            return redirect()->to($link);
        }
    }

    public function markAllAsRead()
    {
        DB::table('notifications')
            ->where('notifiable_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        session()->flash('success', 'All notifications marked as read.');
    }

    public function clearAll()
    {
        DB::table('notifications')
            ->where('notifiable_id', auth()->id())
            ->delete();

        session()->flash('success', 'All notifications cleared.');
    }

    public function render()
    {
        $userId = auth()->id();

        $unreadCount = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->count();

        $notifications = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($item) {
                $item->data_array = json_decode($item->data, true) ?? [];
                $item->formatted_time = \Illuminate\Support\Carbon::parse($item->created_at)->diffForHumans();
                return $item;
            });

        return view('livewire.header-notifications', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
