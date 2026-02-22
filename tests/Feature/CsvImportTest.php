<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\CsvImportService;
use Illuminate\Support\Facades\Storage;
use App\Models\StgPegawaiImport;

class CsvImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_10_rows_into_staging()
    {
        // HEADER 67 kolom
        $header = implode('|', array_fill(0, 67, 'COL'));

        // Buat 10 row dummy 67 kolom
        $rows = [];
        for ($i = 1; $i <= 10; $i++) {
            $row = [];
            for ($c = 0; $c < 67; $c++) {
                $row[] = "VAL{$i}_{$c}";
            }
            $rows[] = implode('|', $row);
        }

        $csvContent = $header . "\n" . implode("\n", $rows);

        // Simpan file sementara
        $path = storage_path('app/test_import.csv');
        file_put_contents($path, $csvContent);

        // Jalankan service
        $service = new CsvImportService();
        $service->import($path, 'test_import.csv');

        // Assert masuk 10 row
        $this->assertEquals(10, StgPegawaiImport::count());

        // Hapus file test
        unlink($path);
    }
}