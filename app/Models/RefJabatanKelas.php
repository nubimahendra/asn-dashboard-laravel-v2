<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefJabatanKelas extends Model
{
    use HasFactory;

    protected $table = 'ref_jabatan_kelas';

    protected $fillable = [
        'jabatan_id',
        'unor_id',
        'kelas_jabatan',
    ];

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'jabatan_id');
    }

    public function unor()
    {
        return $this->belongsTo(RefUnor::class, 'unor_id');
    }
}
