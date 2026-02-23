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
        'last_sync_at' => 'datetime',
    ];
}
