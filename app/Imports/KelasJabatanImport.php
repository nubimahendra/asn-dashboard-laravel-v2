<?php

namespace App\Imports;

use App\Models\RefJabatan;
use App\Models\RefJabatanKelas;
use App\Models\RefUnor;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class KelasJabatanImport implements ToCollection, WithHeadingRow
{
    private $successCount = 0;
    private $errorCount = 0;
    private $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            // Row number in excel (approximate)
            $rowNum = $index + 2;

            if (!isset($row['nama_opd']) || !isset($row['nama_jabatan']) || !isset($row['kelas_jabatan'])) {
                $this->errorCount++;
                $this->errors[] = "Baris {$rowNum}: Header kolom tidak valid atau data kosong.";
                continue;
            }

            $opdName = trim($row['nama_opd']);
            $jabatanName = trim($row['nama_jabatan']);
            $kelas = trim($row['kelas_jabatan']);

            if (empty($opdName) || empty($jabatanName) || $kelas === '') {
                $this->errorCount++;
                $this->errors[] = "Baris {$rowNum}: Terdapat kolom yang kosong.";
                continue;
            }

            // Find Unor
            $unor = RefUnor::where('nama_opd', $opdName)->first();
            if (!$unor) {
                $unor = RefUnor::where('nama', $opdName)->first();
            }

            if (!$unor) {
                $this->errorCount++;
                $this->errors[] = "Baris {$rowNum}: OPD '{$opdName}' tidak ditemukan.";
                continue;
            }

            // Find Jabatan
            $jabatan = RefJabatan::where('nama', $jabatanName)->first();
            if (!$jabatan) {
                $this->errorCount++;
                $this->errors[] = "Baris {$rowNum}: Jabatan '{$jabatanName}' tidak ditemukan.";
                continue;
            }

            try {
                RefJabatanKelas::updateOrCreate(
                    [
                        'jabatan_id' => $jabatan->id,
                        'unor_id' => $unor->id
                    ],
                    [
                        'kelas_jabatan' => $kelas
                    ]
                );
                $this->successCount++;
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = "Baris {$rowNum}: Gagal menyimpan data.";
                Log::error("KelasJabatanImport Error on Row {$rowNum}: " . $e->getMessage());
            }
        }
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getErrorCount()
    {
        return $this->errorCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
