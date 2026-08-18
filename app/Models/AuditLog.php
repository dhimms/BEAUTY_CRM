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
    // morpTho ini mirip dengan relasi hanya saja dia lebih ringkas  
    // morpTho digunakan biasanya karena dia bisa merekam aktivitas untuk beberapa model yang berbeda 
    // morphTo ini akan memanggil model lain berdasarkan data dari auditable_type dan auditable_id
    // auditable_type ini akan menyimpan nama model yang dia rekam aktivitasnya  
    // auditable_id ini akan menyimpan id dari model yang dia rekam aktivitasnya
    public function auditable()
    {
        return $this->morphTo();
    }

    // ─── Scopes ──────────────────────────────────────
    // scope adalah sebuah function yang digunakan untuk memfilter data berdasarkan kondisi yang kita ingin kan atau bisa dubilang kita membuat query secara custom
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
    // Bisa dipanggil di Blade: $log->action_color
    // bisa di panggil di blade karena menggunakan get di awal function  
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created' => 'emerald',
            'updated' => 'blue',
            'deleted' => 'red',
            default => 'gray',
        };
    }

    // bisa di panggil di blade  
    public function getModuleNameAttribute(): string
    {   // class_basename() = ambil nama class paling belakang saja  
        return class_basename($this->auditable_type);    
    }   
}