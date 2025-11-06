<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class Create extends Component
{
    public $name, $sku, $description, $type = 'product', $price = 0, $cost = 0;
    public $stock_quantity = 0, $reorder_point, $reorder_quantity, $unit = 'pcs';
    public $category, $supplier, $is_active = true, $track_inventory = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'required|string|max:255|unique:products,sku',
        'description' => 'nullable|string',
        'type' => 'required|in:product,service,part',
        'price' => 'required|numeric|min:0',
        'cost' => 'required|numeric|min:0',
        'stock_quantity' => 'required|integer|min:0',
        'reorder_point' => 'nullable|integer|min:0',
        'reorder_quantity' => 'nullable|integer|min:0',
        'unit' => 'required|string',
        'category' => 'nullable|string',
        'supplier' => 'nullable|string',
    ];

    public function save()
    {
        $this->authorize('create_products');
        $validated = $this->validate();
        $validated['team_id'] = auth()->user()->currentTeam->id;
        $validated['is_active'] = $this->is_active;
        $validated['track_inventory'] = $this->track_inventory;

        Product::create($validated);
        session()->flash('success', 'Product created successfully.');
        return redirect()->route('products.index');
    }

    public function render()
    {
        return view('livewire.products.create');
    }
}
