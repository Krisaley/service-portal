<?php

namespace App\Models;

use App\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory, HasTeamScope;

    protected $fillable = [
        'team_id',
        'ticket_number',
        'customer_id',
        'asset_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'priority',
        'status',
        'category',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (!$ticket->ticket_number) {
                $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    /**
     * Get only customer-visible comments
     */
    public function customerComments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->where('is_internal', false);
    }

    /**
     * Get only internal comments
     */
    public function internalComments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->where('is_internal', true);
    }

    /**
     * Check if ticket is open
     */
    public function isOpen(): bool
    {
        return in_array($this->status, ['new', 'open', 'pending']);
    }

    /**
     * Check if ticket is closed
     */
    public function isClosed(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Get priority color class
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'bg-red-100 text-red-800',
            'high' => 'bg-orange-100 text-orange-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'low' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'new' => 'bg-purple-100 text-purple-800',
            'open' => 'bg-blue-100 text-blue-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
