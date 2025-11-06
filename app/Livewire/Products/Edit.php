<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class Edit extends Component
{
    public Product $product;
    public $name, $sku, $description, $type, $price, $cost;
    public $stock_quantity, $reorder_point, $reorder_quantity, $unit;
    public $category, $supplier, $is_active, $track_inventory;

    public function mount(Product $product)
    {
        $this->authorize('edit_products');
        $this->product = $product;
        $this->fill($product->only([
            'name', 'sku', 'description', 'type', 'price', 'cost',
            'stock_quantity', 'reorder_point', 'reorder_quantity', 'unit',
            'category', 'supplier', 'is_active', 'track_inventory'
        ]));
    }

    public function update()
    {
        $this->authorize('edit_products');
        $this->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $this->product->id,
            'type' => 'required|in:product,service,part',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
        ]);

        $this->product->update($this->only([
            'name', 'sku', 'description', 'type', 'price', 'cost',
            'stock_quantity', 'reorder_point', 'reorder_quantity', 'unit',
            'category', 'supplier', 'is_active', 'track_inventory'
        ]));

        session()->flash('success', 'Product updated successfully.');
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.edit');
    }
}
