<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanCerai extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_cerai';

    protected $fillable = [
        'nip',
        'nama',
        'jabatan',
        'tanggal_surat',
        'jenis_pengajuan',
        'unit_kerja',
        'opd',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
    ];
}
