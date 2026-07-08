<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'order',
        'probability',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'probability' => 'integer',
        ];
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    // Scope: ordered
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}