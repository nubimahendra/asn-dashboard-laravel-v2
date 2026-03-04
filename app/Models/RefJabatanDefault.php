<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefJabatanDefault extends Model
{
    use HasFactory;

    protected $table = 'ref_jabatan_default';
    protected $primaryKey = 'jabatan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'jabatan_id',
        'kelas_jabatan',
    ];

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'jabatan_id');
    }
}
