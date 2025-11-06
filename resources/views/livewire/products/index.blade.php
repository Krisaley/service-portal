<div>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products & Inventory</h2>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Product
                </a>
            </div>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <input wire:model.live="search" type="text" placeholder="Search products..." class="rounded-md border-gray-300" />
                        <select wire:model.live="filterType" class="rounded-md border-gray-300">
                            <option value="">All Types</option>
                            <option value="product">Product</option>
                            <option value="service">Service</option>
                            <option value="part">Part</option>
                        </select>
                        <select wire:model.live="filterStockStatus" class="rounded-md border-gray-300">
                            <option value="">All Stock Status</option>
                            <option value="low">Low Stock</option>
                            <option value="out">Out of Stock</option>
                        </select>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($products as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $product->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($product->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($product->track_inventory)
                                            <span class="{{ $product->needsReorder() ? 'text-red-600 font-bold' : '' }}">
                                                {{ $product->stock_quantity }} {{ $product->unit }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                        <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a href="{{ route('products.edit', $product) }}" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                        <button wire:click="delete({{ $product->id }})" wire:confirm="Delete this product?" class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="mt-4">{{ $products->links() }}</div>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
