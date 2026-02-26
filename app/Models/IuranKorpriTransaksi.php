<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranKorpriTransaksi extends Model
{
    use HasFactory;

    protected $table = 'iuran_korpri_transaksi';

    protected $fillable = [
        'pegawai_id',
        'kelas_jabatan',
        'nominal',
        'bulan',
        'tahun',
        'status'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
