<?php

namespace App\Services;

use App\Models\{
    RefAgama,
    RefJenisKawin,
    RefJenisPegawai,
    RefKedudukanHukum,
    RefGolongan,
    RefJenisJabatan,
    RefJabatan,
    RefTingkatPendidikan,
    RefPendidikan,
    RefUnor,
    RefInstansi,
    RefLokasi,
    RefKpkn,
    RefJenisAsn
};

class ReferenceSyncService
{
    /**
     * Sync reference data - insert if not exists
     * Returns the ID of the synced record
     */

    public function syncAgama($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefAgama::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncJenisKawin($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefJenisKawin::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncJenisPegawai($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefJenisPegawai::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncKedudukanHukum($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefKedudukanHukum::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncGolongan($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefGolongan::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncJenisJabatan($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefJenisJabatan::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncJabatan($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefJabatan::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncTingkatPendidikan($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefTingkatPendidikan::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncPendidikan($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefPendidikan::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncUnor($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefUnor::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncInstansi($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefInstansi::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncLokasi($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefLokasi::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncKpkn($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefKpkn::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    public function syncJenisAsn($id, $nama)
    {
        if (!$id || !$nama)
            return null;

        RefJenisAsn::firstOrCreate(
            ['id' => $id],
            ['nama' => $nama]
        );

        return $id;
    }

    /**
     * Sync all reference data from staging record
     */
    public function syncAllFromStaging($staging)
    {
        $this->syncAgama($staging->agama_id, $staging->agama);
        $this->syncJenisKawin($staging->jenis_kawin_id, $staging->jenis_kawin);
        $this->syncJenisPegawai($staging->jenis_pegawai_id, $staging->jenis_pegawai);
        $this->syncKedudukanHukum($staging->kedudukan_hukum_id, $staging->kedudukan_hukum);
        $this->syncGolongan($staging->gol_awal_id, $staging->gol_awal);
        $this->syncGolongan($staging->gol_akhir_id, $staging->gol_akhir);
        $this->syncJenisJabatan($staging->jenis_jabatan_id, $staging->jenis_jabatan);
        $this->syncJabatan($staging->jabatan_id, $staging->jabatan);
        $this->syncTingkatPendidikan($staging->tingkat_pendidikan_id, $staging->tingkat_pendidikan);
        $this->syncPendidikan($staging->pendidikan_id, $staging->pendidikan);
        $this->syncUnor($staging->unor_id, $staging->unor);
        $this->syncInstansi($staging->instansi_induk_id, $staging->instansi_induk);
        $this->syncInstansi($staging->instansi_kerja_id, $staging->instansi_kerja);
        $this->syncLokasi($staging->lokasi_kerja_id, $staging->lokasi_kerja);
        $this->syncKpkn($staging->kpkn_id, $staging->kpkn);
    }
}
