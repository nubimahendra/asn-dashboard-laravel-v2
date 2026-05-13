<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranOverrideLog extends Model
{
    use HasFactory;

    protected $table = 'iuran_override_log';
    
    public $timestamps = false; // Only created_at is managed manually

    protected $fillable = [
        'pegawai_id',
        'action',
        'old_golongan_key',
        'new_golongan_key',
        'old_eselon_key',
        'new_eselon_key',
        'alasan',
        'performed_by',
        'created_at',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
