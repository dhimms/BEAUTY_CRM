<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Spatie adalah  package yang digunakan untuk membedakan role user 
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{  // use untuk memanggil package yang digunakan
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'is_active',
        'monthly_target',
        'revenue_target',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'monthly_target' => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────

    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedDeals()
    {
        return $this->hasMany(Deal::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }


    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Helpers ─────────────────────────────────────
     
    // method untuk mengecek apakah user ini rolenya apa
    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isSales(): bool
    {
        return $this->hasRole('Sales');
    }

    //  method isCS() digunakan untuk mengecek apakah user memiliki role Customer Service
    public function isCS(): bool
    {
        return $this->hasRole('Customer Service');
    }

    public function isManager(): bool
    {
        return $this->hasRole('Manager');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=F43F5E&background=FEE2E2';
    }

    public function getRoleBadgeColorAttribute(): string
    {
        return match ($this->getRoleNames()->first()) {
            'Admin' => 'rose',
            'Sales' => 'blue',
            'Customer Service' => 'emerald',
            'Manager' => 'amber',
            default => 'gray',
        };
    }
}