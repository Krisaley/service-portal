<div>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Details</h2>
                <div class="space-x-2">
                    <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">Edit</a>
                    <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Back</a>
                </div>
            </div>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <dl class="grid grid-cols-2 gap-4">
                        <div><dt class="font-semibold text-gray-500">Name</dt><dd class="mt-1">{{ $product->name }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">SKU</dt><dd class="mt-1">{{ $product->sku }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Type</dt><dd class="mt-1">{{ ucfirst($product->type) }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Unit</dt><dd class="mt-1">{{ $product->unit }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Price</dt><dd class="mt-1">${{ number_format($product->price, 2) }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Cost</dt><dd class="mt-1">${{ number_format($product->cost, 2) }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Profit Margin</dt><dd class="mt-1">{{ number_format($product->profit_margin, 2) }}%</dd></div>
                        <div><dt class="font-semibold text-gray-500">Stock</dt><dd class="mt-1">{{ $product->stock_quantity }} {{ $product->unit }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Reorder Point</dt><dd class="mt-1">{{ $product->reorder_point ?? 'Not set' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Reorder Qty</dt><dd class="mt-1">{{ $product->reorder_quantity ?? 'Not set' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Category</dt><dd class="mt-1">{{ $product->category ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Supplier</dt><dd class="mt-1">{{ $product->supplier ?? 'N/A' }}</dd></div>
                        <div class="col-span-2"><dt class="font-semibold text-gray-500">Description</dt><dd class="mt-1">{{ $product->description ?? 'N/A' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Status</dt><dd class="mt-1">{{ $product->is_active ? 'Active' : 'Inactive' }}</dd></div>
                        <div><dt class="font-semibold text-gray-500">Inventory Tracking</dt><dd class="mt-1">{{ $product->track_inventory ? 'Yes' : 'No' }}</dd></div>
                    </dl>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
