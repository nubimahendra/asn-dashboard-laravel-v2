<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekapIuranBulanan extends Model
{
    use HasFactory;

    protected $table = 'rekap_iuran_bulanan';
    
    protected $casts = [
        'breakdown_golongan' => 'array'
    ];
    
    protected $fillable = [
        'nama_opd',
        'bulan',
        'tahun',
        'total_pegawai',
        'total_struktural',
        'total_non_struktural',
        'total_iuran',
        'breakdown_golongan',
        'created_by'
    ];
}
