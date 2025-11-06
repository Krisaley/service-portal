<div>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assets</h2>
                <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Add Asset</a>
            </div>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
                @endif
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input wire:model.live="search" type="text" placeholder="Search assets..." class="rounded-md border-gray-300" />
                        <select wire:model.live="filterType" class="rounded-md border-gray-300">
                            <option value="">All Types</option>
                            <option value="equipment">Equipment</option>
                            <option value="license">License</option>
                            <option value="rams">RAMS</option>
                            <option value="insurance">Insurance</option>
                            <option value="client_asset">Client Asset</option>
                        </select>
                        <select wire:model.live="filterStatus" class="rounded-md border-gray-300">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="retired">Retired</option>
                        </select>
                        <select wire:model.live="filterAlert" class="rounded-md border-gray-300">
                            <option value="">All Alerts</option>
                            <option value="service_due">Service Due</option>
                            <option value="expiring">Expiring Soon</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alerts</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($assets as $asset)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium">{{ $asset->name }}</div>
                                        @if($asset->serial_number)<div class="text-xs text-gray-500">SN: {{ $asset->serial_number }}</div>@endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ ucfirst(str_replace('_', ' ', $asset->asset_type)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $asset->customer?->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            {{ $asset->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $asset->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $asset->status === 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                            {{ $asset->status === 'retired' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($asset->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($asset->needsService())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Service Due</span>
                                        @elseif($asset->serviceDueSoon())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Service Soon</span>
                                        @endif
                                        @if($asset->isExpired())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                        @elseif($asset->expiryApproaching())
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Expiring Soon</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('assets.show', $asset) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a href="{{ route('assets.edit', $asset) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <button wire:click="delete({{ $asset->id }})" wire:confirm="Delete this asset?" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No assets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $assets->links() }}</div>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
