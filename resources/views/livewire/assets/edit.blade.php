<div>
    <x-app-layout>
        <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Asset</h2></x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form wire:submit="update">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label>Name *</label><input wire:model="name" type="text" class="w-full rounded-md border-gray-300" required /></div>
                            <div><label>Type *</label><select wire:model="asset_type" class="w-full rounded-md border-gray-300"><option value="equipment">Equipment</option><option value="license">License</option><option value="rams">RAMS</option><option value="insurance">Insurance</option><option value="client_asset">Client Asset</option></select></div>
                            <div><label>Customer</label><select wire:model="customer_id" class="w-full rounded-md border-gray-300"><option value="">None</option>@foreach($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach</select></div>
                            <div><label>Status *</label><select wire:model="status" class="w-full rounded-md border-gray-300"><option value="active">Active</option><option value="inactive">Inactive</option><option value="maintenance">Maintenance</option><option value="retired">Retired</option></select></div>
                            <div><label>Serial Number</label><input wire:model="serial_number" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Model</label><input wire:model="model" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Manufacturer</label><input wire:model="manufacturer" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Location</label><input wire:model="location" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div class="col-span-2"><label>Description</label><textarea wire:model="description" rows="2" class="w-full rounded-md border-gray-300"></textarea></div>
                            <div><label>Last Service Date</label><input wire:model="last_service_date" type="date" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Next Service Date</label><input wire:model="next_service_date" type="date" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Service Frequency</label><select wire:model="service_frequency" class="w-full rounded-md border-gray-300"><option value="">None</option><option value="daily">Daily</option><option value="weekly">Weekly</option><option value="monthly">Monthly</option><option value="quarterly">Quarterly</option><option value="annually">Annually</option><option value="custom">Custom</option></select></div>
                            <div><label>Frequency (Days)</label><input wire:model="service_frequency_days" type="number" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Expiry Date</label><input wire:model="expiry_date" type="date" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Warning Days</label><input wire:model="expiry_warning_days" type="number" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Purchase Date</label><input wire:model="purchase_date" type="date" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Purchase Cost</label><input wire:model="purchase_cost" type="number" step="0.01" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Supplier</label><input wire:model="supplier" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Warranty Expiry</label><input wire:model="warranty_expiry" type="text" class="w-full rounded-md border-gray-300" /></div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Asset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
