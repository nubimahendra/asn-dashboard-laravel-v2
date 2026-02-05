<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatStatusPegawai extends Model
{
    use HasFactory;

    protected $table = 'riwayat_status_pegawai';

    protected $fillable = [
        'pegawai_id',
        'status',
        'tmt',
        'keterangan',
    ];

    protected $casts = [
        'tmt' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
