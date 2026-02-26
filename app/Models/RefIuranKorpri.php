<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefIuranKorpri extends Model
{
    use HasFactory;

    protected $table = 'ref_iuran_korpri';

    protected $fillable = [
        'kelas_jabatan',
        'nominal',
        'tahun_berlaku'
    ];
}
