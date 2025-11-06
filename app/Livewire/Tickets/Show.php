<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\TicketComment;
use Livewire\Component;

class Show extends Component
{
    public Ticket $ticket;
    public $newComment = '';
    public $isInternal = false;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket->load(['customer', 'asset', 'assignedTo', 'createdBy', 'comments.user']);
    }

    public function addComment()
    {
        $this->validate([
            'newComment' => 'required|string|min:1',
        ]);

        TicketComment::create([
            'ticket_id' => $this->ticket->id,
            'user_id' => auth()->id(),
            'comment' => $this->newComment,
            'is_internal' => $this->isInternal,
        ]);

        $this->newComment = '';
        $this->isInternal = false;

        $this->ticket->refresh()->load('comments.user');

        session()->flash('success', 'Comment added successfully.');
    }

    public function render()
    {
        return view('livewire.tickets.show');
    }
}
