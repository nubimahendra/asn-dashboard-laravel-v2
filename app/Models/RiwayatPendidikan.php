<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatPendidikan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_pendidikan';

    protected $fillable = [
        'pegawai_id',
        'pendidikan_id',
        'tingkat_pendidikan_id',
        'tahun_lulus',
        'institusi',
        'keterangan',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function pendidikan()
    {
        return $this->belongsTo(RefPendidikan::class, 'pendidikan_id');
    }

    public function tingkatPendidikan()
    {
        return $this->belongsTo(RefTingkatPendidikan::class, 'tingkat_pendidikan_id');
    }
}
