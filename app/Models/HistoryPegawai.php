<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryPegawai extends Model
{
    use HasFactory;

    protected $table = 'history_pegawai';

    protected $fillable = [
        'nip_baru',
        'nama_pegawai',
        'tgl_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'agama',
        'jenis_kawin',
        'eselon',
        'jabatan',
        'pd',
        'sub_pd',
        'jenikel',
        'sts_peg',
        'jenis_pegawai',
        'tk_pend',
        'golongan',
        'jenis_jabatan',
        'unor_nama',
        'unor_opd',
        'pendidikan',
        'tingkat_pendidikan',
        'status_cpns_pns',
        'tmt_cpns',
        'tmt_pns',
        'kedudukan_hukum',
        'no_hp',
        'last_sync_at',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'last_sync_at' => 'datetime',
    ];
}
