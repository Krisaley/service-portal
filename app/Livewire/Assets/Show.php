<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use Livewire\Component;

class Show extends Component
{
    public Asset $asset;

    public function mount(Asset $asset)
    {
        $this->authorize('view_assets');
        $this->asset = $asset->load('customer', 'maintenanceLogs.performedBy');
    }

    public function render()
    {
        return view('livewire.assets.show');
    }
}
