<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Create extends Component
{
    public $name = '';
    public $email = '';
    public $phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $zip = '';
    public $country = '';
    public $vat_number = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:255',
        'city' => 'nullable|string|max:255',
        'state' => 'nullable|string|max:255',
        'zip' => 'nullable|string|max:255',
        'country' => 'nullable|string|max:255',
        'vat_number' => 'nullable|string|max:255',
    ];

    public function save()
    {
        $this->authorize('create_customers');

        $this->validate();

        Customer::create([
            'team_id' => auth()->user()->currentTeam->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'vat_number' => $this->vat_number,
        ]);

        session()->flash('success', 'Customer created successfully.');

        return redirect()->route('customers.index');
    }

    public function render()
    {
        return view('livewire.customers.create');
    }
}
