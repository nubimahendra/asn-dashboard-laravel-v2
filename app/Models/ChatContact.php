<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'remote_id',
        'name',
        'number',
    ];
}
