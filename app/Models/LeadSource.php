<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeadSource extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'color',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }
}