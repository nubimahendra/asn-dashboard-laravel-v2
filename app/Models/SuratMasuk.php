<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_agenda',
        'nomor_surat',
        'pengirim',
        'perihal',
        'disposisi',
        'keterangan',
        'tanggal_terima',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
    ];
}
