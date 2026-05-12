<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefIuranEselon extends Model
{
    use HasFactory;

    protected $table = 'ref_iuran_eselon';

    protected $fillable = [
        'eselon_key',
        'label',
        'besaran',
    ];
}
