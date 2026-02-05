<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatGolongan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_golongan';

    protected $fillable = [
        'pegawai_id',
        'golongan_id',
        'tmt',
        'mk_tahun',
        'mk_bulan',
        'keterangan',
    ];

    protected $casts = [
        'tmt' => 'date',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function golongan()
    {
        return $this->belongsTo(RefGolongan::class, 'golongan_id');
    }
}
