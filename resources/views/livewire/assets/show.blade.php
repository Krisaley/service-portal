<div>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Asset Details</h2>
                <div class="space-x-2">
                    <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Edit</a>
                    <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Back</a>
                </div>
            </div>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div><dt class="font-semibold text-gray-500">Name</dt><dd class="mt-1">{{ $asset->name }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Type</dt><dd class="mt-1">{{ ucfirst(str_replace('_', ' ', $asset->asset_type)) }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Customer</dt><dd class="mt-1">{{ $asset->customer?->name ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Status</dt><dd class="mt-1">{{ ucfirst($asset->status) }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Serial Number</dt><dd class="mt-1">{{ $asset->serial_number ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Model</dt><dd class="mt-1">{{ $asset->model ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Manufacturer</dt><dd class="mt-1">{{ $asset->manufacturer ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Location</dt><dd class="mt-1">{{ $asset->location ?? 'N/A' }}</dd></div>
                        <div class="col-span-2"><dt class="font-semibold text-gray-500">Description</dt><dd class="mt-1">{{ $asset->description ?? 'N/A' }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Maintenance Schedule</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div><dt class="font-semibold text-gray-500">Last Service</dt><dd class="mt-1">{{ $asset->last_service_date?->format('M d, Y') ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Next Service</dt><dd class="mt-1 {{ $asset->needsService() ? 'text-red-600 font-bold' : '' }}">{{ $asset->next_service_date?->format('M d, Y') ?? 'N/A' }}@if($asset->needsService()) (Overdue!)@elseif($asset->serviceDueSoon()) (Due Soon)@endif</dd></div>
                        <div><dt class="font-semibold text-gray-500">Frequency</dt><dd class="mt-1">{{ $asset->service_frequency ? ucfirst($asset->service_frequency) : 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Days Until Service</dt><dd class="mt-1">{{ $asset->daysUntilService() ?? 'N/A' }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Expiry & Purchase Information</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div><dt class="font-semibold text-gray-500">Expiry Date</dt><dd class="mt-1 {{ $asset->isExpired() ? 'text-red-600 font-bold' : ($asset->expiryApproaching() ? 'text-orange-600 font-semibold' : '') }}">{{ $asset->expiry_date?->format('M d, Y') ?? 'N/A' }}@if($asset->isExpired()) (Expired!)@elseif($asset->expiryApproaching()) (Expiring Soon)@endif</dd></div>
                        <div><dt class="font-semibold text-gray-500">Days Until Expiry</dt><dd class="mt-1">{{ $asset->daysUntilExpiry() ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Purchase Date</dt><dd class="mt-1">{{ $asset->purchase_date?->format('M d, Y') ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Purchase Cost</dt><dd class="mt-1">{{ $asset->purchase_cost ? '$'.number_format($asset->purchase_cost, 2) : 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Supplier</dt><dd class="mt-1">{{ $asset->supplier ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Warranty Expiry</dt><dd class="mt-1">{{ $asset->warranty_expiry ?? 'N/A' }}</dd></div>
                    </dl>
                </div>

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Maintenance History</h3>
                    @if($asset->maintenanceLogs->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performed By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($asset->maintenanceLogs as $log)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->maintenance_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ ucfirst($log->type) }}</td>
                                        <td class="px-6 py-4 text-sm">{{ $log->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->performedBy?->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $log->cost ? '$'.number_format($log->cost, 2) : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">No maintenance history recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
