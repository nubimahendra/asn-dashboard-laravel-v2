<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IuranOverride extends Model
{
    use HasFactory;

    protected $table = 'iuran_override';

    protected $fillable = [
        'pegawai_id',
        'override_golongan_key',
        'override_eselon_key',
        'alasan',
        'updated_by',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }
}
