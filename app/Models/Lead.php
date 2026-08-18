<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Lead (Calon Pelanggan)
 * Digunakan di: LeadController, DashboardController, DealService, dan Tampilan Views Leads.
 * Fungsi: Mengelola data calon pelanggan yang didapatkan Sales (sumber, status, kualifikasi).
 */
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

    // ─── Relationships (Relasi Antar Tabel) ─────────

    // Relasi ke tabel LeadSource (sumber lead: Instagram, WA, dll)
    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    // Relasi ke tabel User (sales yang bertanggung jawab)
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relasi ke tabel User (pembuat data lead)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke tabel Deals (transaksi deal yang dibuat dari lead ini)
    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    // Relasi ke tabel Activities (log aktivitas follow-up lead)
    public function activities()
    {
        return $this->morphMany(Activity::class, 'activitable');
    }

    // Relasi ke tabel Customer (jika deal won, lead jadi customer)
    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    // ─── Scopes (Filter Query Data) ──────────────────

    // Filter daftar lead berdasarkan status (default: tidak menampilkan yang sudah converted)
    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status === 'all') {
            return $query;
        }

        if ($status) {
            return $query->where('status', $status);
        }
        
        return $query->where('status', '!=', 'converted');
    }

    // Filter lead berdasarkan sumber asal lead (Instagram, WA, Website, dll)
    public function scopeFilterSource($query, ?int $sourceId)
    {
        return $sourceId ? $query->where('lead_source_id', $sourceId) : $query;
    }

    // Filter lead berdasarkan kualifikasi (qualified, unqualified, win, lost)
    public function scopeFilterQualification($query, ?string $qualification)
    {
        if (!$qualification) {
            return $query;
        }

        if (in_array($qualification, ['win', 'lost'])) {
            return $query->whereHas('deals', function ($q) use ($qualification) {
                $q->where('status', $qualification === 'win' ? 'won' : 'lost');
            });
        }

        return $query->where('qualification', $qualification);
    }

    // Filter lead berdasarkan sales yang memegang (assigned_to)
    public function scopeFilterAssigned($query, ?int $userId)
    {
        return $userId ? $query->where('assigned_to', $userId) : $query;
    }

    // Filter pencarian lead berdasarkan nama, email, atau nomor HP
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

    // ─── Accessors (Format Tampilan Warna Badge UI) ─

    // Menentukan warna badge untuk status lead di Blade view
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'blue',
            'contacted' => 'purple',
            'qualified' => 'amber',
            'converted' => 'emerald',
            'closed' => 'gray',
            'win' => 'blue',
            'lost' => 'gray',
            default => 'gray',
        };
    }

    // Menentukan warna badge untuk kualifikasi lead di Blade view
    public function getQualificationColorAttribute(): string
    {
        return match ($this->qualification) {
            'qualified' => 'emerald',
            'unqualified' => 'amber',
            'not_fit' => 'red',
            'win' => 'blue',
            'lost' => 'gray',
            default => 'gray',
        };
    }
}