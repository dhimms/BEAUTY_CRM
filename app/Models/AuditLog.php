<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    // ─── Scopes ──────────────────────────────────────

    public function scopeFilterAction($query, ?string $action)
    {
        return $action ? $query->where('action', $action) : $query;
    }

    public function scopeFilterUser($query, ?int $userId)
    {
        return $userId ? $query->where('user_id', $userId) : $query;
    }

    public function scopeFilterModule($query, ?string $module)
    {
        return $module ? $query->where('auditable_type', 'like', "%{$module}%") : $query;
    }

    // ─── Accessors ───────────────────────────────────

    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'emerald',
            'updated' => 'blue',
            'deleted' => 'red',
            default => 'gray',
        };
    }

    public function getModuleNameAttribute(): string
    {
        return class_basename($this->auditable_type);
    }
}