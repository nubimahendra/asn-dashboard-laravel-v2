<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IuranKorpri extends Model
{
    protected $table = 'iuran_korpri';

    protected $fillable = [
        'golongan_key',
        'label',
        'besaran',
    ];
}
