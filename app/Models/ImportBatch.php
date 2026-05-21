<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_file',
        'import_target',
        'total_rows',
        'valid_rows',
        'invalid_rows',
        'skipped_rows',
        'processed_count',
        'error_count',
        'summary_new',
        'summary_changed',
        'summary_unchanged',
        'deactivated_count',
        'status',
        'total_pegawai_before',
        'total_pegawai_after',
        'summary_imported',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    public function imports()
    {
        return $this->hasMany(StgPegawaiImport::class, 'batch_id');
    }
}
