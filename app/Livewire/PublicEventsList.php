<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Event;
use App\Enums\EventStatus;

#[Layout('layouts.guest')]
#[Title('Explore Public Events — AttendFlow')]
class PublicEventsList extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = ''; // 'free', 'paid'
    public $perPage = 9;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Event::where('status', EventStatus::Published)->where('is_private', false);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('venue_name', 'like', '%' . $this->search . '%')
                  ->orWhere('venue_city', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter === 'free') {
            $query->where('is_free', true);
        } elseif ($this->typeFilter === 'paid') {
            $query->where('is_free', false);
        }

        $events = $query->orderBy('starts_at', 'asc')->paginate($this->perPage);

        return view('livewire.public-events-list', [
            'events' => $events
        ]);
    }
}
