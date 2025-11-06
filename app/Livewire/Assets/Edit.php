<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\Customer;
use Livewire\Component;

class Edit extends Component
{
    public Asset $asset;
    public $name, $asset_type, $serial_number, $model, $manufacturer;
    public $description, $location, $status, $customer_id;
    public $last_service_date, $next_service_date, $service_frequency, $service_frequency_days;
    public $expiry_date, $expiry_warning_days;
    public $purchase_date, $purchase_cost, $supplier, $warranty_expiry;

    public function mount(Asset $asset)
    {
        $this->authorize('edit_assets');
        $this->asset = $asset;
        $this->fill($asset->only([
            'name', 'asset_type', 'serial_number', 'model', 'manufacturer',
            'description', 'location', 'status', 'customer_id',
            'last_service_date', 'next_service_date', 'service_frequency', 'service_frequency_days',
            'expiry_date', 'expiry_warning_days',
            'purchase_date', 'purchase_cost', 'supplier', 'warranty_expiry'
        ]));
    }

    public function update()
    {
        $this->authorize('edit_assets');
        $this->validate([
            'name' => 'required|string|max:255',
            'asset_type' => 'required|string',
            'customer_id' => 'nullable|exists:customers,id',
            'status' => 'required|in:active,inactive,maintenance,retired',
        ]);

        $this->asset->update($this->only([
            'name', 'asset_type', 'serial_number', 'model', 'manufacturer',
            'description', 'location', 'status', 'customer_id',
            'last_service_date', 'next_service_date', 'service_frequency', 'service_frequency_days',
            'expiry_date', 'expiry_warning_days',
            'purchase_date', 'purchase_cost', 'supplier', 'warranty_expiry'
        ]));

        session()->flash('success', 'Asset updated successfully.');
        return redirect()->route('assets.index');
    }

    public function render()
    {
        $customers = Customer::where('team_id', auth()->user()->currentTeam->id)
            ->orderBy('name')
            ->get();

        return view('livewire.assets.edit', compact('customers'));
    }
}
