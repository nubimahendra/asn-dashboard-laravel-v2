<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pegawai';

    protected $fillable = [
        'pns_id',
        'nip_baru',
        'nip_lama',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'alamat',
        'no_hp',
        'email',
        'agama_id',
        'jenis_kawin_id',
        'jenis_pegawai_id',
        'kedudukan_hukum_id',
        'golongan_id',
        'jabatan_id',
        'jenis_jabatan_id',
        'pendidikan_id',
        'tingkat_pendidikan_id',
        'unor_id',
        'instansi_induk_id',
        'instansi_kerja_id',
        'lokasi_kerja_id',
        'kpkn_id',
        'status_cpns_pns',
        'tmt_cpns',
        'tmt_pns',
        'flag_ikd',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tmt_cpns' => 'date',
        'tmt_pns' => 'date',
    ];

    // Relationships to Reference Tables
    public function agama()
    {
        return $this->belongsTo(RefAgama::class, 'agama_id');
    }

    public function jenisKawin()
    {
        return $this->belongsTo(RefJenisKawin::class, 'jenis_kawin_id');
    }

    public function jenisPegawai()
    {
        return $this->belongsTo(RefJenisPegawai::class, 'jenis_pegawai_id');
    }

    public function kedudukanHukum()
    {
        return $this->belongsTo(RefKedudukanHukum::class, 'kedudukan_hukum_id');
    }

    public function golongan()
    {
        return $this->belongsTo(RefGolongan::class, 'golongan_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(RefJabatan::class, 'jabatan_id');
    }

    public function jenisJabatan()
    {
        return $this->belongsTo(RefJenisJabatan::class, 'jenis_jabatan_id');
    }

    public function pendidikan()
    {
        return $this->belongsTo(RefPendidikan::class, 'pendidikan_id');
    }

    public function tingkatPendidikan()
    {
        return $this->belongsTo(RefTingkatPendidikan::class, 'tingkat_pendidikan_id');
    }

    public function unor()
    {
        return $this->belongsTo(RefUnor::class, 'unor_id');
    }

    public function instansiInduk()
    {
        return $this->belongsTo(RefInstansi::class, 'instansi_induk_id');
    }

    public function instansiKerja()
    {
        return $this->belongsTo(RefInstansi::class, 'instansi_kerja_id');
    }

    public function lokasiKerja()
    {
        return $this->belongsTo(RefLokasi::class, 'lokasi_kerja_id');
    }

    public function kpkn()
    {
        return $this->belongsTo(RefKpkn::class, 'kpkn_id');
    }

    // Relationships to History Tables
    public function riwayatStatus()
    {
        return $this->hasMany(RiwayatStatusPegawai::class);
    }

    public function riwayatGolongan()
    {
        return $this->hasMany(RiwayatGolongan::class);
    }

    public function riwayatJabatan()
    {
        return $this->hasMany(RiwayatJabatan::class);
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    // Helper method to get full name with titles
    public function getNamaLengkapAttribute()
    {
        $nama = [];
        if ($this->gelar_depan)
            $nama[] = $this->gelar_depan;
        $nama[] = $this->nama;
        if ($this->gelar_belakang)
            $nama[] = $this->gelar_belakang;

        return implode(' ', $nama);
    }
}
