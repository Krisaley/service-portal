<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Customer;
use Livewire\Component;

class Create extends Component
{
    public $name, $asset_type = 'equipment', $serial_number, $model, $manufacturer;
    public $description, $location, $status = 'active', $customer_id;
    public $last_service_date, $next_service_date, $service_frequency, $service_frequency_days;
    public $expiry_date, $expiry_warning_days = 30;
    public $purchase_date, $purchase_cost, $supplier, $warranty_expiry;

    protected $rules = [
        'name' => 'required|string|max:255',
        'asset_type' => 'required|string',
        'customer_id' => 'nullable|exists:customers,id',
        'serial_number' => 'nullable|string',
        'model' => 'nullable|string',
        'status' => 'required|in:active,inactive,maintenance,retired',
    ];

    public function save()
    {
        $this->authorize('create_assets');
        $validated = $this->validate();

        $asset = Asset::create([
            'team_id' => auth()->user()->currentTeam->id,
            ...$validated,
            'manufacturer' => $this->manufacturer,
            'description' => $this->description,
            'location' => $this->location,
            'last_service_date' => $this->last_service_date,
            'next_service_date' => $this->next_service_date,
            'service_frequency' => $this->service_frequency,
            'service_frequency_days' => $this->service_frequency_days,
            'expiry_date' => $this->expiry_date,
            'expiry_warning_days' => $this->expiry_warning_days,
            'purchase_date' => $this->purchase_date,
            'purchase_cost' => $this->purchase_cost,
            'supplier' => $this->supplier,
            'warranty_expiry' => $this->warranty_expiry,
        ]);

        session()->flash('success', 'Asset created successfully.');
        return redirect()->route('assets.index');
    }

    public function render()
    {
        $customers = Customer::where('team_id', auth()->user()->currentTeam->id)
            ->orderBy('name')
            ->get();

        return view('livewire.assets.create', compact('customers'));
    }
}
