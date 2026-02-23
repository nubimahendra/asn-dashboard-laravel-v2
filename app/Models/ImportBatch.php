<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_file',
        'total_rows',
        'valid_rows',
        'invalid_rows',
        'status',
    ];

    public function imports()
    {
        return $this->hasMany(StgPegawaiImport::class, 'batch_id');
    }
}
