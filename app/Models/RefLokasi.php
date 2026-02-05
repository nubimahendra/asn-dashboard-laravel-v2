<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefLokasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ref_lokasi';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'nama'];
}
