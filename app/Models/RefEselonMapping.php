<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefEselonMapping extends Model
{
    use HasFactory;

    protected $table = 'ref_eselon_mapping';

    protected $fillable = [
        'jabatan_id',
        'eselon_key',
        'is_auto',
    ];

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'jabatan_id', 'id');
    }
}
