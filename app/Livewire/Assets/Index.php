<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterAlert = '';

    protected $queryString = ['search', 'filterType', 'filterStatus', 'filterAlert'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $this->authorize('delete_assets');
        Asset::findOrFail($id)->delete();
        session()->flash('success', 'Asset deleted successfully.');
    }

    public function render()
    {
        $assets = Asset::with('customer')
            ->where('team_id', auth()->user()->currentTeam->id)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('serial_number', 'like', '%' . $this->search . '%')
                      ->orWhere('model', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterType, fn($q) => $q->where('asset_type', $this->filterType))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterAlert === 'service_due', function($q) {
                $q->whereNotNull('next_service_date')
                  ->where('next_service_date', '<=', now()->addDays(7));
            })
            ->when($this->filterAlert === 'expiring', function($q) {
                $q->whereNotNull('expiry_date')
                  ->where('expiry_date', '<=', now()->addDays(30))
                  ->where('expiry_date', '>', now());
            })
            ->when($this->filterAlert === 'expired', function($q) {
                $q->whereNotNull('expiry_date')
                  ->where('expiry_date', '<', now());
            })
            ->orderBy('name')
            ->paginate(15);

        return view('livewire.assets.index', ['assets' => $assets]);
    }
}
