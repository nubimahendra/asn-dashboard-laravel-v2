<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefOpdMapping extends Model
{
    use HasFactory;

    protected $table = 'ref_opd_mapping';

    protected $fillable = [
        'unor_siasn_id',
        'nama_opd_perbup',
        'status_validasi',
        'catatan',
    ];

    public function unorSiasn()
    {
        return $this->belongsTo(RefUnor::class, 'unor_siasn_id');
    }
}
