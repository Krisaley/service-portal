<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';
    public $filterPriority = '';

    protected $queryString = ['search', 'filterStatus', 'filterPriority'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tickets = Ticket::with(['customer', 'assignedTo', 'createdBy'])
            ->where('team_id', auth()->user()->currentTeam->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('ticket_number', 'like', '%' . $this->search . '%')
                      ->orWhere('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('livewire.tickets.index', ['tickets' => $tickets]);
    }
}
