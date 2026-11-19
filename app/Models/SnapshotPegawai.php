<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SnapshotPegawai extends Model
{
    use HasFactory;

    protected $table = 'snapshot_pegawai';

    protected $fillable = [
        'nip_baru',
        'nama_pegawai',
        'gelar_depan',
        'gelar_belakang',
        'mk_tahun',
        'mk_bulan',
        'kedudukan_hukum_id',
        'kedudukan_hukum',
        'jenis_pegawai',
        'sumber',
        'tgl_lahir',
        'eselon',
        'jabatan',
        'pd',
        'sub_pd',
        'jenikel',
        'sts_peg',
        'tk_pend',
        'golongan',
        'unor_id',
        'last_sync_at',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'last_sync_at' => 'datetime',
    ];
}
