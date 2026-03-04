<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefKelasPerbup extends Model
{
    use HasFactory;

    protected $table = 'ref_kelas_perbup';

    protected $fillable = [
        'nama_opd_perbup',
        'nama_jabatan_perbup',
        'kelas_jabatan',
    ];

    public function jabatanMappings()
    {
        return $this->hasMany(RefJabatanMapping::class, 'kelas_perbup_id');
    }
}
