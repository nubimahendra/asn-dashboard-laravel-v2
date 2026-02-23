<?php

namespace App\Services;

use App\Models\StgPegawaiImport;
use Exception;

class PegawaiValidationService
{
    /**
     * Validate a staging row. Throws an Exception if any required field is missing.
     * 
     * @param StgPegawaiImport $row
     * @return void
     * @throws Exception
     */
    public function validate(StgPegawaiImport $row)
    {
        $errors = [];

        if (empty($row->pns_id)) {
            $errors[] = 'PNS ID wajib diisi.';
        }
        if (empty($row->nip_baru)) {
            $errors[] = 'NIP Baru wajib diisi.';
        }
        if (empty($row->nama)) {
            $errors[] = 'Nama wajib diisi.';
        }

        if (!empty($errors)) {
            throw new Exception(implode(' ', $errors));
        }
    }
}
