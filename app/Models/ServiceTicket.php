<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'assigned_to',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'attachments',
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    // ─── Boot ────────────────────────────────────────

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            $ticket->ticket_number = 'TKT-' . str_pad(
                static::withTrashed()->count() + 1,
                4,
                '0',
                STR_PAD_LEFT
            );
        });
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['closed']);
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search)
            return $query;
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('ticket_number', 'like', "%{$search}%");
        });
    }

    // ─── Accessors ───────────────────────────────────

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'gray',
            'medium' => 'amber',
            'high' => 'red',
            'urgent' => 'rose',
            default => 'gray',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'blue',
            'in_progress' => 'amber',
            'resolved' => 'emerald',
            'closed' => 'gray',
            default => 'gray',
        };
    }
}