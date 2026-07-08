<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'activitable_type',
        'activitable_id',
        'type',
        'subject',
        'description',
        'duration',
        'result',
        'activity_date',
        'follow_up_date',
        'follow_up_type',
        'follow_up_notes',
        'follow_up_status',
    ];

    protected function casts(): array
    {
        return [
            'activity_date' => 'datetime',
            'follow_up_date' => 'date',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activitable()
    {
        return $this->morphTo();
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopePendingFollowUps($query)
    {
        return $query->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '>=', now()->toDateString())
            ->orderBy('follow_up_date');
    }

    public function scopeOverdueFollowUps($query)
    {
        return $query->where('follow_up_status', 'pending')
            ->whereNotNull('follow_up_date')
            ->where('follow_up_date', '<', now()->toDateString());
    }

    // ─── Accessors ───────────────────────────────────

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'call' => 'blue',
            'whatsapp' => 'emerald',
            'email' => 'purple',
            'meeting' => 'amber',
            'note' => 'gray',
            default => 'gray',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'call' => 'phone',
            'whatsapp' => 'message-circle',
            'email' => 'mail',
            'meeting' => 'users',
            'note' => 'file-text',
            default => 'activity',
        };
    }
}