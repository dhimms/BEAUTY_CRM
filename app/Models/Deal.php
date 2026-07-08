<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'name',
        'value',
        'pipeline_stage_id',
        'status',
        'lost_reason_id',
        'lost_notes',
        'expected_close_date',
        'closed_at',
        'assigned_to',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'expected_close_date' => 'date',
            'closed_at' => 'datetime',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function lostReason()
    {
        return $this->belongsTo(LostReason::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeWon($query)
    {
        return $query->where('status', 'won');
    }

    public function scopeLost($query)
    {
        return $query->where('status', 'lost');
    }

    public function scopeFilterStatus($query, ?string $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeFilterStage($query, ?int $stageId)
    {
        return $stageId ? $query->where('pipeline_stage_id', $stageId) : $query;
    }

    // ─── Helpers ─────────────────────────────────────

    public function getWeightedValueAttribute(): float
    {
        $probability = $this->pipelineStage?->probability ?? 0;
        return $this->value * ($probability / 100);
    }

    public function getFormattedValueAttribute(): string
    {
        return 'Rp ' . number_format($this->value, 0, ',', '.');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open' => 'blue',
            'won' => 'emerald',
            'lost' => 'red',
            default => 'gray',
        };
    }
}