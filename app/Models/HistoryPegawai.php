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
        'eselon',
        'jabatan',
        'pd',
        'sub_pd',
        'jenikel',
        'sts_peg',
        'tk_pend',
        'golongan',
        'no_hp',
        'last_sync_at',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'last_sync_at' => 'datetime',
    ];
}
