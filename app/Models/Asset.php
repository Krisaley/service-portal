<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory, HasTeamScope;

    protected $fillable = [
        'team_id',
        'customer_id',
        'name',
        'asset_type',
        'serial_number',
        'model',
        'manufacturer',
        'description',
        'location',
        'status',
        'last_service_date',
        'next_service_date',
        'service_frequency',
        'service_frequency_days',
        'expiry_date',
        'expiry_warning_days',
        'purchase_date',
        'purchase_cost',
        'supplier',
        'warranty_expiry',
        'custom_fields',
    ];

    protected $casts = [
        'last_service_date' => 'date',
        'next_service_date' => 'date',
        'expiry_date' => 'date',
        'purchase_date' => 'date',
        'purchase_cost' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(AssetMaintenanceLog::class);
    }

    /**
     * Check if asset needs service
     */
    public function needsService(): bool
    {
        if (!$this->next_service_date) {
            return false;
        }

        return $this->next_service_date <= now();
    }

    /**
     * Check if asset service is due soon
     */
    public function serviceDueSoon(int $days = 7): bool
    {
        if (!$this->next_service_date) {
            return false;
        }

        return $this->next_service_date <= now()->addDays($days) &&
               $this->next_service_date > now();
    }

    /**
     * Check if asset is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date < now();
    }

    /**
     * Check if asset expiry is approaching
     */
    public function expiryApproaching(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $warningDate = now()->addDays($this->expiry_warning_days);
        return $this->expiry_date <= $warningDate && !$this->isExpired();
    }

    /**
     * Calculate next service date based on frequency
     */
    public function calculateNextServiceDate(?Carbon $fromDate = null): ?Carbon
    {
        $fromDate = $fromDate ?? ($this->last_service_date ?? now());

        return match($this->service_frequency) {
            'daily' => $fromDate->copy()->addDay(),
            'weekly' => $fromDate->copy()->addWeek(),
            'monthly' => $fromDate->copy()->addMonth(),
            'quarterly' => $fromDate->copy()->addMonths(3),
            'annually' => $fromDate->copy()->addYear(),
            'custom' => $this->service_frequency_days
                ? $fromDate->copy()->addDays($this->service_frequency_days)
                : null,
            default => null,
        };
    }

    /**
     * Get days until next service
     */
    public function daysUntilService(): ?int
    {
        if (!$this->next_service_date) {
            return null;
        }

        return now()->diffInDays($this->next_service_date, false);
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }
}
