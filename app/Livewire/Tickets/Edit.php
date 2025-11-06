<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Customer;
use App\Models\Asset;
use Livewire\Component;

class Edit extends Component
{
    public Ticket $ticket;
    public $title, $description, $priority, $status;
    public $customer_id, $asset_id, $assigned_to, $category;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->fill($ticket->only([
            'title', 'description', 'priority', 'status',
            'customer_id', 'asset_id', 'assigned_to', 'category'
        ]));
    }

    public function update()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,normal,high,urgent',
            'status' => 'required|in:new,open,pending,resolved,closed',
        ]);

        $this->ticket->update($this->only([
            'title', 'description', 'priority', 'status',
            'customer_id', 'asset_id', 'assigned_to', 'category'
        ]));

        if ($this->status === 'resolved' && !$this->ticket->resolved_at) {
            $this->ticket->update(['resolved_at' => now()]);
        }
        if ($this->status === 'closed' && !$this->ticket->closed_at) {
            $this->ticket->update(['closed_at' => now()]);
        }

        session()->flash('success', 'Ticket updated successfully.');
        return redirect()->route('tickets.show', $this->ticket);
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $customers = Customer::where('team_id', $team->id)->orderBy('name')->get();
        $assets = Asset::where('team_id', $team->id)->orderBy('name')->get();
        $users = $team->allUsers();

        return view('livewire.tickets.edit', compact('customers', 'assets', 'users'));
    }
}
