<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Livewire\Component;

class Show extends Component
{
    public Customer $customer;

    public function mount(Customer $customer)
    {
        $this->authorize('view_customers');
        $this->customer = $customer;
    }

    public function render()
    {
        return view('livewire.customers.show');
    }
}
