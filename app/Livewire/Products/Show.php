<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;

class Show extends Component
{
    public Product $product;

    public function mount(Product $product)
    {
        $this->authorize('view_products');
        $this->product = $product;
    }

    public function render()
    {
        return view('livewire.products.show');
    }
}
