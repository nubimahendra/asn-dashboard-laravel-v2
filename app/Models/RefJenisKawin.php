<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RefJenisKawin extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ref_jenis_kawin';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['id', 'nama'];
}
