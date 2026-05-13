<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property Carbon|null $tanggal_lahir
 * @property Carbon|null $tmt_cpns
 * @property Carbon|null $tmt_pns
 */
class Pegawai extends Model
{
    use HasFactory;

    /**
     * Kedudukan hukum IDs that indicate an active ASN employee.
     */
    const ACTIVE_KEDUDUKAN_HUKUM = ['01', '02', '03', '04', '101', '15', '71', '73'];

    /**
     * Scope: Only active employees (not kedudukan_hukum_id = 17/Non Aktif)
     */
    public function scopeAktif($query)
    {
        return $query->where(function ($q) {
            $q->whereIn('kedudukan_hukum_id', self::ACTIVE_KEDUDUKAN_HUKUM)
              ->orWhereNull('kedudukan_hukum_id');
        });
    }

    protected $table = 'pegawai';

    protected $fillable = [
        'pns_id',
        'nik',
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
        'data_hash',
        'is_anomali',
        'catatan_anomali',
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

    public function riwayatJabatanAktif()
    {
        return $this->hasOne(RiwayatJabatan::class)->latest('tmt');
    }

    public function riwayatPendidikan()
    {
        return $this->hasMany(RiwayatPendidikan::class);
    }

    public function iuranOverride()
    {
        return $this->hasOne(IuranOverride::class, 'pegawai_id');
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

    // Helper method to get golongan converted for PPPK
    public function getGolonganPppkAttribute()
    {
        $namaGolongan = trim($this->golongan->nama ?? '');

        // Cek apakah pegawai ini PPPK Aktif
        $isPppkAktif = false;
        if (isset($this->kedudukan_hukum_id) && in_array($this->kedudukan_hukum_id, ['71', '73'])) {
            $isPppkAktif = true;
        } elseif (isset($this->kedudukanHukum->nama) && strtolower(trim($this->kedudukanHukum->nama)) == 'pppk aktif') {
            $isPppkAktif = true;
        }

        // PNS: langsung return nama golongan asli (I/a, II/b, dst)
        if (!$isPppkAktif) {
            return $namaGolongan;
        }

        // PPPK Aktif: konversi golongan ke format PPPK
        return match($namaGolongan) {
            'I/a', 'I/b', 'I'    => 'I',
            'II/a', 'V'           => 'V',
            'II/b', 'VI'          => 'VI',   // tambah VI jika ada
            'II/c', 'VII'         => 'VII',
            'III/a', 'IX'         => 'IX',
            'III/b', 'X'          => 'X',
            'III/c', 'XI'         => 'XI',
            default               => $namaGolongan,
        };
    }
}
