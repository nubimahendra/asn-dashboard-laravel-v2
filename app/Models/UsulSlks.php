<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsulSlks extends Model
{
    use HasFactory;

    protected $table = 'usul_slks';
    
    protected $fillable = [
        'nip', 'nama', 'pangkat', 'jabatan',
        'no_sk_hukdis', 'tmt_hukdis', 'no_sk_cltn', 'tmt_cltn',
        'kabkota', 'provinsi', 'kd_wil',
        'slks_ada', 'no_slks', 'tgl_slks',
        'usul_slks', 'masa_kerja_tahun', 'masa_kerja_bulan',
        'bulanp', 'tahunp', 'ms_tms', 'ket_tms',
        'status', 'jenis_pegawai', 'kedudukan_hukum_id',
        'created_by', 'updated_by', 'catatan',
        'no_kepres', 'tanggal_kepres',
    ];
    
    protected $casts = [
        'tmt_hukdis' => 'date',
        'tmt_cltn' => 'date',
        'tgl_slks' => 'date',
        'tanggal_kepres' => 'date',
    ];
    
    // Scopes
    public function scopeRiwayat($query) { 
        return $query->where('status', 'riwayat'); 
    }
    
    public function scopeUsulan($query) { 
        return $query->whereIn('status', ['draft_usulan', 'diajukan']); 
    }
    
    // Relations
    public function creator() { 
        return $this->belongsTo(User::class, 'created_by'); 
    }
    
    public function updater() { 
        return $this->belongsTo(User::class, 'updated_by'); 
    }
}
