<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Pegawai;
use App\Models\StgPegawaiImport;
use App\Jobs\ProcessPegawaiImport;

class ProcessPegawaiImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_soft_deletes_removed_employees_when_flag_is_true()
    {
        // 1. Create employees in the database
        $keptPnsId = 'existing-id-1';
        $removedPnsId = 'removed-id-2';

        $keptId = DB::table('pegawai')->insertGetId([
            'pns_id' => $keptPnsId,
            'nip_baru' => '1234567890',
            'nama' => 'Kept Employee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $removedId = DB::table('pegawai')->insertGetId([
            'pns_id' => $removedPnsId,
            'nip_baru' => '0987654321',
            'nama' => 'Removed Employee',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $filename = 'test_sync.csv';

        // 2. Create staging records (import file) that ONLY contains the kept employee
        DB::table('stg_pegawai_import')->insert([
            'source_file' => $filename,
            'pns_id' => $keptPnsId,
            'sync_status' => 'unchanged',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Dispatch the job WITH deleteRemoved = true
        $job = new ProcessPegawaiImport($filename, true);
        $job->handle(app(\App\Services\PegawaiImportService::class));

        // 4. Assert the kept employee is still in the database
        $this->assertDatabaseHas('pegawai', [
            'id' => $keptId,
            'deleted_at' => null
        ]);

        // 5. Assert the removed employee is softly deleted (deleted_at is not null)
        $this->assertSoftDeleted('pegawai', [
            'id' => $removedId
        ]);
    }

    public function test_does_not_soft_delete_when_flag_is_false()
    {
        // 1. Create employees
        $keptPnsId = 'id-1';
        $removedPnsId = 'id-2';

        $keptId = DB::table('pegawai')->insertGetId([
            'pns_id' => $keptPnsId,
            'nip_baru' => '111',
            'nama' => 'Kept 2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $removedId = DB::table('pegawai')->insertGetId([
            'pns_id' => $removedPnsId,
            'nip_baru' => '222',
            'nama' => 'Removed 2',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $filename = 'test_sync_no_delete.csv';

        // 2. Staging has only id-1
        DB::table('stg_pegawai_import')->insert([
            'source_file' => $filename,
            'pns_id' => $keptPnsId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Dispatch the job WITHOUT deleteRemoved flag (defaults to false)
        $job = new ProcessPegawaiImport($filename, false);
        $job->handle(app(\App\Services\PegawaiImportService::class));

        // 4. Assert both employees are STILL active
        $this->assertDatabaseHas('pegawai', [
            'id' => $keptId,
            'deleted_at' => null
        ]);

        $this->assertDatabaseHas('pegawai', [
            'id' => $removedId,
            'deleted_at' => null
        ]);
    }
}
