<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'status',
        'tags',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'status' => 'string',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function csUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    public function deals()
    {
        return $this->hasMany(Deal::class, 'lead_id', 'lead_id');
    }


    // ─── Scopes ──────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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

    public function scopeMinSpend($query, ?float $minSpend)
    {
        if (!$minSpend) return $query;
        return $query->whereRaw("IFNULL((SELECT SUM(value) FROM deals WHERE deals.lead_id = customers.lead_id AND deals.status = 'won'), 0) >= ?", [$minSpend]);
    }

    public function scopeHasDealName($query, ?string $dealKeyword)
    {
        if (!$dealKeyword) return $query;
        return $query->whereHas('deals', function ($q) use ($dealKeyword) {
            $q->where('name', 'like', "%{$dealKeyword}%");
        });
    }
}