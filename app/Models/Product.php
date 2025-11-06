<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasTeamScope;

    protected $fillable = [
        'team_id',
        'name',
        'sku',
        'description',
        'type',
        'price',
        'cost',
        'stock_quantity',
        'reorder_point',
        'reorder_quantity',
        'unit',
        'category',
        'supplier',
        'is_active',
        'track_inventory',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'reorder_point' => 'integer',
        'reorder_quantity' => 'integer',
        'is_active' => 'boolean',
        'track_inventory' => 'boolean',
    ];

    /**
     * Check if product needs reordering
     */
    public function needsReorder(): bool
    {
        if (!$this->track_inventory || !$this->reorder_point) {
            return false;
        }

        return $this->stock_quantity <= $this->reorder_point;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        if (!$this->track_inventory) {
            return true;
        }

        return $this->stock_quantity > 0;
    }

    /**
     * Get profit margin
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->price == 0) {
            return 0;
        }

        return (($this->price - $this->cost) / $this->price) * 100;
    }
}
