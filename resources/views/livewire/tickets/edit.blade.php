<div>
    <x-app-layout>
        <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Ticket</h2></x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form wire:submit="update">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2"><label>Title *</label><input wire:model="title" type="text" class="w-full rounded-md border-gray-300" required /></div>
                            <div class="col-span-2"><label>Description *</label><textarea wire:model="description" rows="4" class="w-full rounded-md border-gray-300" required></textarea></div>
                            <div><label>Customer</label><select wire:model="customer_id" class="w-full rounded-md border-gray-300"><option value="">Select Customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}">{{ $customer->name }}</option>@endforeach</select></div>
                            <div><label>Asset</label><select wire:model="asset_id" class="w-full rounded-md border-gray-300"><option value="">Select Asset</option>@foreach($assets as $asset)<option value="{{ $asset->id }}">{{ $asset->name }}</option>@endforeach</select></div>
                            <div><label>Priority *</label><select wire:model="priority" class="w-full rounded-md border-gray-300"><option value="low">Low</option><option value="normal">Normal</option><option value="high">High</option><option value="urgent">Urgent</option></select></div>
                            <div><label>Status *</label><select wire:model="status" class="w-full rounded-md border-gray-300"><option value="new">New</option><option value="open">Open</option><option value="pending">Pending</option><option value="resolved">Resolved</option><option value="closed">Closed</option></select></div>
                            <div><label>Assign To</label><select wire:model="assigned_to" class="w-full rounded-md border-gray-300"><option value="">Unassigned</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }}</option>@endforeach</select></div>
                            <div><label>Category</label><input wire:model="category" type="text" class="w-full rounded-md border-gray-300" /></div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('tickets.show', $ticket) }}" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Ticket</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
