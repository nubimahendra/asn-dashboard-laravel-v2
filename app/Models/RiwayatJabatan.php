<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatJabatan extends Model
{
    use HasFactory;

    protected $table = 'riwayat_jabatan';

    protected $fillable = [
        'pegawai_id',
        'jabatan_id',
        'jenis_jabatan_id',
        'unor_id',
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

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'jabatan_id');
    }

    public function jenisJabatan()
    {
        return $this->belongsTo(RefJenisJabatan::class, 'jenis_jabatan_id');
    }

    public function unor()
    {
        return $this->belongsTo(RefUnor::class, 'unor_id');
    }
}
