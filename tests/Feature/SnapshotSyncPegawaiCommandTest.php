<?php

namespace Tests\Feature;

use App\Models\SnapshotPegawai;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Tes untuk command snapshot:sync-pegawai.
 *
 * Catatan skema: tabel snapshot_pegawai dibentuk lewat migrasi asli repo
 * (create + no_hp + tgl_lahir + golongan + unor_id) yang dijalankan dengan
 * `migrate --path`, bukan RefreshDatabase. RefreshDatabase menjalankan SELURUH
 * migrasi dan salah satunya (2026_05_21_035212_fix_ref_golongan_pppk_collision)
 * memakai fungsi MySQL CONCAT sehingga gagal di sqlite. Dengan --path kita tetap
 * menguji skema sungguhan, termasuk kolom unor_id hasil migrasi baru.
 *
 * Tabel sumber export_pegawai tidak punya migrasi di repo (milik DB Sidawai),
 * jadi skemanya dibuat manual mengikuti kolom yang dipakai PegawaiSyncService.
 */
class SnapshotSyncPegawaiCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->jalankanMigrasiSnapshot();
        $this->siapkanKoneksiSidawai();
    }

    private function jalankanMigrasiSnapshot(): void
    {
        $this->artisan('migrate', [
            '--path' => [
                base_path('database/migrations/2026_01_08_034845_create_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_01_09_025041_add_no_hp_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_01_21_000002_add_tgl_lahir_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_01_23_032218_add_golongan_to_snapshot_pegawai_table.php'),
                base_path('database/migrations/2026_11_05_220716_add_unor_id_to_snapshot_pegawai_table.php'),
            ],
            '--realpath' => true,
        ])->assertExitCode(0);
    }

    private function siapkanKoneksiSidawai(): void
    {
        config([
            'database.connections.sidawai' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);

        DB::purge('sidawai');

        Schema::connection('sidawai')->create('export_pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('nip_baru');
            $table->string('nama_pegawai');
            $table->date('tgl_lahir')->nullable();
            $table->string('eselon')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('pd')->nullable();
            $table->string('sub_pd')->nullable();
            $table->string('jenikel')->nullable();
            $table->string('sts_peg')->nullable();
            $table->string('tk_pend')->nullable();
            $table->string('golru')->nullable();
            $table->string('unor_id')->nullable();
        });
    }

    private function isiSumber(array $rows): void
    {
        DB::connection('sidawai')->table('export_pegawai')->insert($rows);
    }

    private function barisSumber(string $nip, array $override = []): array
    {
        return array_merge([
            'nip_baru' => $nip,
            'nama_pegawai' => 'Pegawai ' . $nip,
            'tgl_lahir' => '1985-03-12',
            'eselon' => 'III/a',
            'jabatan' => 'Analis',
            'pd' => 'Dinas Pendidikan',
            'sub_pd' => 'Bidang Sekolah Dasar',
            'jenikel' => 'L',
            'sts_peg' => 'PNS',
            'tk_pend' => 'S1',
            'golru' => 'III/a',
            'unor_id' => 'U-001',
        ], $override);
    }

    public function test_menyalin_baris_sumber_ke_snapshot_termasuk_unor_id_dan_golongan(): void
    {
        $this->isiSumber([
            $this->barisSumber('199001012015011001', ['unor_id' => 'U-AAA', 'golru' => 'III/a']),
            $this->barisSumber('199002022016012002', ['unor_id' => 'U-BBB', 'golru' => 'IV/b', 'nama_pegawai' => 'Pegawai Kedua']),
        ]);

        // chunk=1 memaksa dua batch, sekaligus membuktikan offset bertambah benar
        $this->artisan('snapshot:sync-pegawai', ['--chunk' => 1])
            ->expectsOutputToContain('Total baris di sumber: 2')
            ->assertExitCode(Command::SUCCESS);

        $this->assertSame(2, SnapshotPegawai::count());

        $pertama = SnapshotPegawai::where('nip_baru', '199001012015011001')->firstOrFail();
        $this->assertSame('U-AAA', $pertama->unor_id);
        $this->assertSame('III/a', $pertama->golongan);
        $this->assertSame('1985-03-12', $pertama->tgl_lahir->format('Y-m-d'));
        $this->assertNotNull($pertama->last_sync_at);

        $kedua = SnapshotPegawai::where('nip_baru', '199002022016012002')->firstOrFail();
        $this->assertSame('U-BBB', $kedua->unor_id);
        $this->assertSame('IV/b', $kedua->golongan);
        $this->assertSame('Pegawai Kedua', $kedua->nama_pegawai);
    }

    public function test_jalan_kedua_idempoten_dan_memperbarui_data(): void
    {
        $this->isiSumber([$this->barisSumber('199003032017011003')]);

        $this->artisan('snapshot:sync-pegawai')->assertExitCode(Command::SUCCESS);
        $this->assertSame(1, SnapshotPegawai::count());

        // Sumber berubah: jalan kedua harus update, bukan menambah baris baru
        DB::connection('sidawai')->table('export_pegawai')
            ->where('nip_baru', '199003032017011003')
            ->update(['nama_pegawai' => 'Nama Baru', 'unor_id' => 'U-BARU', 'golru' => 'IV/a']);

        $this->artisan('snapshot:sync-pegawai')->assertExitCode(Command::SUCCESS);

        $this->assertSame(1, SnapshotPegawai::count());

        $snapshot = SnapshotPegawai::where('nip_baru', '199003032017011003')->firstOrFail();
        $this->assertSame('Nama Baru', $snapshot->nama_pegawai);
        $this->assertSame('U-BARU', $snapshot->unor_id);
        $this->assertSame('IV/a', $snapshot->golongan);
    }

    public function test_cleanup_menghapus_nip_yang_sudah_tidak_ada_di_sumber(): void
    {
        $this->isiSumber([$this->barisSumber('199004042018011004')]);

        SnapshotPegawai::create([
            'nip_baru' => '199999999999999999',
            'nama_pegawai' => 'Pegawai Basi',
        ]);

        $this->artisan('snapshot:sync-pegawai')
            ->expectsOutputToContain('Cleanup: 1 baris snapshot dihapus.')
            ->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseMissing('snapshot_pegawai', ['nip_baru' => '199999999999999999']);
        $this->assertDatabaseHas('snapshot_pegawai', ['nip_baru' => '199004042018011004']);
    }

    public function test_no_cleanup_menahan_baris_basi(): void
    {
        $this->isiSumber([$this->barisSumber('199005052019011005')]);

        SnapshotPegawai::create([
            'nip_baru' => '199999999999999998',
            'nama_pegawai' => 'Pegawai Basi',
        ]);

        $this->artisan('snapshot:sync-pegawai', ['--no-cleanup' => true])
            ->assertExitCode(Command::SUCCESS);

        $this->assertDatabaseHas('snapshot_pegawai', ['nip_baru' => '199999999999999998']);
        $this->assertDatabaseHas('snapshot_pegawai', ['nip_baru' => '199005052019011005']);
    }

    public function test_koneksi_sumber_gagal_keluar_dengan_status_failure_tanpa_kredensial(): void
    {
        config([
            'database.connections.sidawai' => [
                'driver' => 'sqlite',
                'database' => database_path('tidak-ada-di-sini.sqlite'),
                'prefix' => '',
                'foreign_key_constraints' => false,
            ],
        ]);
        DB::purge('sidawai');

        $this->artisan('snapshot:sync-pegawai')
            ->expectsOutputToContain('Gagal membaca sumber Sidawai')
            ->assertExitCode(Command::FAILURE);
    }
}
