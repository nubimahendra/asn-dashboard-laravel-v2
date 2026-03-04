<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefJabatanMapping extends Model
{
    use HasFactory;

    protected $table = 'ref_jabatan_mapping';

    protected $fillable = [
        'jabatan_siasn_id',
        'kelas_perbup_id',
        'status_validasi',
        'catatan',
    ];

    public function jabatanSiasn()
    {
        return $this->belongsTo(RefJabatan::class, 'jabatan_siasn_id');
    }

    public function kelasPerbup()
    {
        return $this->belongsTo(RefKelasPerbup::class, 'kelas_perbup_id');
    }
}
