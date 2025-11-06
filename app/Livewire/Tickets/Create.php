<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Models\Customer;
use App\Models\Asset;
use App\Models\User;
use Livewire\Component;

class Create extends Component
{
    public $title, $description, $priority = 'normal', $status = 'new';
    public $customer_id, $asset_id, $assigned_to, $category;

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'priority' => 'required|in:low,normal,high,urgent',
        'status' => 'required|in:new,open,pending,resolved,closed',
        'customer_id' => 'nullable|exists:customers,id',
        'asset_id' => 'nullable|exists:assets,id',
        'assigned_to' => 'nullable|exists:users,id',
        'category' => 'nullable|string',
    ];

    public function save()
    {
        $validated = $this->validate();

        Ticket::create([
            'team_id' => auth()->user()->currentTeam->id,
            'created_by' => auth()->id(),
            ...$validated,
        ]);

        session()->flash('success', 'Ticket created successfully.');
        return redirect()->route('tickets.index');
    }

    public function render()
    {
        $team = auth()->user()->currentTeam;
        $customers = Customer::where('team_id', $team->id)->orderBy('name')->get();
        $assets = Asset::where('team_id', $team->id)->orderBy('name')->get();
        $users = $team->allUsers();

        return view('livewire.tickets.create', compact('customers', 'assets', 'users'));
    }
}
