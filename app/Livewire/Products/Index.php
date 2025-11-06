<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterStockStatus = '';

    protected $queryString = ['search', 'filterType', 'filterStockStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $this->authorize('delete_products');
        Product::findOrFail($id)->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    public function render()
    {
        $products = Product::where('team_id', auth()->user()->currentTeam->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->filterStockStatus === 'low', fn($q) => $q->whereColumn('stock_quantity', '<=', 'reorder_point'))
            ->when($this->filterStockStatus === 'out', fn($q) => $q->where('stock_quantity', 0))
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.products.index', ['products' => $products]);
    }
}
