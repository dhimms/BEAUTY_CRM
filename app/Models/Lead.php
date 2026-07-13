<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'lead_source_id',
        'assigned_to',
        'status',
        'qualification',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'string',
            'qualification' => 'string',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status === 'all') {
            return $query;
        }

        if ($status) {
            return $query->where('status', $status);
        }
        
        // By default, exclude leads that have been converted to Deals/Customers
        return $query->where('status', '!=', 'converted');
    }

    public function scopeFilterSource($query, ?int $sourceId)
    {
        return $sourceId ? $query->where('lead_source_id', $sourceId) : $query;
    }

    public function scopeFilterQualification($query, ?string $qualification)
    {
        return $qualification ? $query->where('qualification', $qualification) : $query;
    }

    public function scopeFilterAssigned($query, ?int $userId)
    {
        return $userId ? $query->where('assigned_to', $userId) : $query;
    }

    public function scopeSearch($query, ?string $search)
    {
        if (!$search)
            return $query;
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // ─── Accessors ───────────────────────────────────

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'blue',
            'contacted' => 'purple',
            'qualified' => 'amber',
            'converted' => 'emerald',
            'closed' => 'gray',
            default => 'gray',
        };
    }

    public function getQualificationColorAttribute(): string
    {
        return match ($this->qualification) {
            'qualified' => 'emerald',
            'unqualified' => 'amber',
            'not_fit' => 'red',
            default => 'gray',
        };
    }
}