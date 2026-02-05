<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StgPegawaiImport extends Model
{
    use HasFactory;

    protected $table = 'stg_pegawai_import';

    protected $fillable = [
        'pns_id',
        'nip_baru',
        'nip_lama',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'agama_id',
        'agama',
        'jenis_kawin_id',
        'jenis_kawin',
        'jenis_pegawai_id',
        'jenis_pegawai',
        'kedudukan_hukum_id',
        'kedudukan_hukum',
        'gol_awal_id',
        'gol_awal',
        'gol_akhir_id',
        'gol_akhir',
        'tmt_gol_akhir',
        'mk_tahun',
        'mk_bulan',
        'jenis_jabatan_id',
        'jenis_jabatan',
        'jabatan_id',
        'jabatan',
        'tmt_jabatan',
        'tingkat_pendidikan_id',
        'tingkat_pendidikan',
        'pendidikan_id',
        'pendidikan',
        'tahun_lulus',
        'unor_id',
        'unor',
        'instansi_induk_id',
        'instansi_induk',
        'instansi_kerja_id',
        'instansi_kerja',
        'lokasi_kerja_id',
        'lokasi_kerja',
        'kpkn_id',
        'kpkn',
        'status_cpns_pns',
        'tmt_cpns',
        'tmt_pns',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_hp',
        'email',
        'flag_ikd',
        'source_file',
        'imported_at',
        'is_processed',
        'processed_at',
        'processing_error',
    ];

    protected $casts = [
        'tmt_gol_akhir' => 'date',
        'tmt_jabatan' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
        'tanggal_lahir' => 'date',
        'imported_at' => 'datetime',
        'processed_at' => 'datetime',
        'is_processed' => 'boolean',
    ];
}
