<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product</h2>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form wire:submit="update">
                        <div class="grid grid-cols-2 gap-4">
                            <div><label>Name *</label><input wire:model="name" type="text" class="w-full rounded-md border-gray-300" required /></div>
                            <div><label>SKU *</label><input wire:model="sku" type="text" class="w-full rounded-md border-gray-300" required /></div>
                            <div><label>Type *</label><select wire:model="type" class="w-full rounded-md border-gray-300"><option value="product">Product</option><option value="service">Service</option><option value="part">Part</option></select></div>
                            <div><label>Unit</label><input wire:model="unit" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Price *</label><input wire:model="price" type="number" step="0.01" class="w-full rounded-md border-gray-300" required /></div>
                            <div><label>Cost *</label><input wire:model="cost" type="number" step="0.01" class="w-full rounded-md border-gray-300" required /></div>
                            <div><label>Stock Quantity</label><input wire:model="stock_quantity" type="number" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Reorder Point</label><input wire:model="reorder_point" type="number" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Reorder Quantity</label><input wire:model="reorder_quantity" type="number" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Category</label><input wire:model="category" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div><label>Supplier</label><input wire:model="supplier" type="text" class="w-full rounded-md border-gray-300" /></div>
                            <div class="col-span-2"><label>Description</label><textarea wire:model="description" rows="3" class="w-full rounded-md border-gray-300"></textarea></div>
                            <div><label class="flex items-center"><input wire:model="track_inventory" type="checkbox" class="rounded border-gray-300" /> Track Inventory</label></div>
                            <div><label class="flex items-center"><input wire:model="is_active" type="checkbox" class="rounded border-gray-300" /> Active</label></div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-2">
                            <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-300 rounded-md">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </x-app-layout>
</div>
