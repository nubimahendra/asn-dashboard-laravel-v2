<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FonnteToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'is_active',
    ];

    protected $casts = [
        'token' => 'encrypted',
        'is_active' => 'boolean',
    ];
}
